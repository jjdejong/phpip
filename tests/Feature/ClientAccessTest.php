<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SampleSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Covers the company-scoped access of client users.
 *
 * Matter PAT001 (id 4) is linked to Tesla Motors Inc. (actor 124) as client in
 * the sample data, and 124 is itself a client login.
 *
 * Note Actor::$guarded blocks 'password', so fixtures below are created without
 * one and authenticated with be() - these tests exercise visibility, not login.
 */
class ClientAccessTest extends TestCase
{
    // Each test runs in a transaction that is rolled back, rather than using
    // TestCase::resetDatabaseAndSeed(): its migrate:rollback reverses the
    // utf8mb4 conversion migration, whose down() fails on this schema.
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // All seeders use insertOrIgnore, so this is a no-op on a seeded
        // database and makes a merely migrated one usable.
        $this->seed(DatabaseSeeder::class);
        $this->seed(SampleSeeder::class);
    }

    /** Create a client login, optionally attached to a company actor. */
    private function clientUser(string $login, ?int $companyId = null, ?string $role = 'CLI'): User
    {
        $actor = Actor::create([
            'name' => strtoupper($login),
            'login' => $login,
            'default_role' => $role,
            'company_id' => $companyId,
            'phy_person' => 1,
        ]);

        return User::find($actor->id);
    }

    /** Link an actor to a matter as client. */
    private function linkAsClient(int $matterId, int $actorId, int $displayOrder = 1): void
    {
        DB::table('matter_actor_lnk')->insert([
            'matter_id' => $matterId,
            'actor_id' => $actorId,
            'role' => 'CLI',
            'display_order' => $displayOrder,
            'shared' => 1,
        ]);
    }

    public function testCompanyContactSeesTheCompanysMatters()
    {
        $this->be($this->clientUser('emusk', 124));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    public function testClientCompanyItselfStillSeesItsMatters()
    {
        $this->be(User::find(124));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /**
     * The scope must work downwards too: a matter linked to a contact has to be
     * visible to the company login, or two logins of one client disagree.
     */
    public function testCompanyUserSeesMattersLinkedToItsContacts()
    {
        $contact = $this->clientUser('emusk', 124);

        // Move matter 4's client link from the company to the contact
        // matter_actors is a read-only view - edit the underlying pivot
        DB::table('matter_actor_lnk')
            ->where([['matter_id', 4], ['role', 'CLI']])
            ->update(['actor_id' => $contact->id]);

        $this->be(User::find(124));
        $this->call('GET', '/matter/4')->assertStatus(200);

        $this->be($contact);
        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /**
     * The reason MatterPolicy uses clients() rather than the hasOne client():
     * a user matching the second link must not be denied.
     */
    public function testUserMatchingASecondClientLinkIsAllowed()
    {
        $contact = $this->clientUser('second', null);
        $this->linkAsClient(4, $contact->id, 2);

        $this->be($contact);
        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /** The real boundary: a contact of one company must not see another's. */
    public function testContactOfAnotherCompanyIsDenied()
    {
        $otherCompany = Actor::create(['name' => 'OTHER Inc.', 'phy_person' => 0]);
        $this->be($this->clientUser('rival', $otherCompany->id));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
    }

    public function testUnrelatedClientIsDenied()
    {
        $this->be($this->clientUser('outsider'));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
    }

    /**
     * An account with a blank role is unconfigured, not a client of its company:
     * it keeps the self-only access it had before company scoping existed.
     */
    public function testEmptyRoleUserGetsNoCompanyScope()
    {
        $this->be($this->clientUser('norole', 124, null));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
    }

    /**
     * A contact keeps their natural CNT role - which is what gets proposed when
     * they are linked to a matter - and still gets their company's matters.
     */
    public function testContactWithCntRoleGetsCompanyScope()
    {
        $this->be($this->clientUser('contact', 124, 'CNT'));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /**
     * Regression: a login whose role is neither CLI nor a staff role used to be
     * treated as staff and could see every matter.
     */
    public function testNonStaffRoleIsRestricted()
    {
        $this->be($this->clientUser('stray', null, 'CNT'));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
        $this->call('GET', '/category')->assertStatus(403);
    }

    /** Internal users (actor 2 = phpipuser, DBA) bypass the client scope. */
    public function testInternalUserSeesEverything()
    {
        $this->be(User::find(2));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /**
     * Every matter sub-resource must run the policy, not just show(). These all
     * hang off the matter page a client legitimately reaches for their own
     * matters, so they are authorized per matter rather than blocked outright.
     */
    public function testMatterSubResourcesAreDeniedToAnUnrelatedClient()
    {
        $this->be($this->clientUser('outsider'));

        foreach ([
            '/matter/4/info',
            '/matter/4/events',
            '/matter/4/tasks',
            '/matter/4/renewals',
            '/matter/4/classifiers',
            '/matter/4/roleActors/CLI',
            '/matter/4/description/en',
        ] as $url) {
            $this->call('GET', $url)->assertStatus(403, "expected 403 from $url");
        }

        $this->call('POST', '/matter/4/mergeFile')->assertStatus(403);
    }

    /** ... and the client of the matter still reaches all of them. */
    public function testMatterSubResourcesStayOpenToTheClientOfTheMatter()
    {
        $this->be($this->clientUser('emusk', 124));

        foreach ([
            '/matter/4/info',
            '/matter/4/events',
            '/matter/4/tasks',
            '/matter/4/renewals',
            '/matter/4/classifiers',
            '/matter/4/roleActors/CLI',
            '/matter/4/description/en',
        ] as $url) {
            $this->call('GET', $url)->assertStatus(200, "expected 200 from $url");
        }
    }

    /** Fetching a task by id must not bypass the scoping applied to /task. */
    public function testTaskShowIsDeniedToAnUnrelatedClient()
    {
        // A client-visible code, so the 403 comes from the policy and not the task scope
        $taskId = DB::table('task')->whereIn('code', config('client.visible_task_codes'))->value('id');
        $this->be($this->clientUser('outsider'));

        $this->call('GET', "/task/$taskId")->assertStatus(403);
    }

    /** The renewal workflow is internal; clients must not reach any of it. */
    public function testRenewalWorkflowIsClosedToClients()
    {
        $this->be($this->clientUser('outsider'));

        $this->call('GET', '/renewal/export')->assertStatus(403);
        $this->call('GET', '/renewal/logs')->assertStatus(403);
        $this->call('POST', '/renewal/paid')->assertStatus(403);
    }

    /** Document generation exposes contact emails, so it follows the policy. */
    public function testDocumentSelectIsDeniedToAnUnrelatedClient()
    {
        $this->be($this->clientUser('outsider'));

        $this->call('GET', '/document/select/4')->assertStatus(403);
    }

    /** Autocomplete must not leak case references outside the client's scope. */
    public function testMatterAutocompleteIsScopedForClients()
    {

        $this->be($this->clientUser('emusk', 124));
        $this->call('GET', '/matter/autocomplete', ['term' => 'PAT'])
            ->assertStatus(200)
            ->assertSee('PAT001');

        $this->be($this->clientUser('outsider'));
        $this->call('GET', '/matter/autocomplete', ['term' => 'PAT'])
            ->assertStatus(200)
            ->assertDontSee('PAT001');
    }

    /** Add an open task of the given code to matter PAT001 (id 4). */
    private function addTaskToPat001(string $code, string $detail): int
    {
        $eventId = DB::table('event')->insertGetId([
            'matter_id' => 4,
            'code' => 'REC',
            'event_date' => now()->toDateString(),
        ]);

        return DB::table('task')->insertGetId([
            'trigger_id' => $eventId,
            'code' => $code,
            'due_date' => now()->addMonth()->toDateString(),
            'detail' => json_encode(['en' => $detail]),
            'done' => 0,
        ]);
    }

    /**
     * Clients see only the whitelisted, official task codes - on the dashboard,
     * the matter page and by id. Internal tasks such as invoicing stay hidden.
     */
    public function testClientSeesOnlyWhitelistedTasks()
    {
        $officialId = $this->addTaskToPat001('REP', 'Official action XYZZY');
        $internalId = $this->addTaskToPat001('PREP', 'Invoice PLUGH');

        $this->be($this->clientUser('emusk', 124));

        $this->call('GET', '/task')
            ->assertStatus(200)
            ->assertSee('XYZZY')
            ->assertDontSee('PLUGH');

        $this->call('GET', '/matter/4')
            ->assertStatus(200)
            ->assertSee('XYZZY')
            ->assertDontSee('PLUGH');

        $this->call('GET', '/matter/4/tasks')
            ->assertStatus(200)
            ->assertSee('XYZZY')
            ->assertDontSee('PLUGH');

        $this->call('GET', "/task/$officialId")->assertStatus(200);
        $this->call('GET', "/task/$internalId")->assertStatus(404);
    }

    /** The whitelist is for clients only; staff still see every task. */
    public function testStaffSeeInternalTasks()
    {
        $internalId = $this->addTaskToPat001('PREP', 'Invoice PLUGH');

        $this->be(User::find(2));

        $this->call('GET', '/task')
            ->assertStatus(200)
            ->assertSee('PLUGH');

        $this->call('GET', "/task/$internalId")->assertStatus(200);
    }
}
