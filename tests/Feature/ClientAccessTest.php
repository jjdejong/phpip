<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\User;
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
        $this->resetDatabaseAndSeed();
        $this->be($this->clientUser('emusk', 124));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    public function testClientCompanyItselfStillSeesItsMatters()
    {
        $this->resetDatabaseAndSeed();
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
        $this->resetDatabaseAndSeed();
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
        $this->resetDatabaseAndSeed();
        $contact = $this->clientUser('second', null);
        $this->linkAsClient(4, $contact->id, 2);

        $this->be($contact);
        $this->call('GET', '/matter/4')->assertStatus(200);
    }

    /** The real boundary: a contact of one company must not see another's. */
    public function testContactOfAnotherCompanyIsDenied()
    {
        $this->resetDatabaseAndSeed();
        $otherCompany = Actor::create(['name' => 'OTHER Inc.', 'phy_person' => 0]);
        $this->be($this->clientUser('rival', $otherCompany->id));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
    }

    public function testUnrelatedClientIsDenied()
    {
        $this->resetDatabaseAndSeed();
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
        $this->resetDatabaseAndSeed();
        $this->be($this->clientUser('norole', 124, null));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertViewHas('matters', fn ($matters) => $matters->isEmpty());

        $this->call('GET', '/matter/4')->assertStatus(403);
    }

    /** Internal users (actor 2 = phpipuser, DBA) bypass the client scope. */
    public function testInternalUserSeesEverything()
    {
        $this->resetDatabaseAndSeed();
        $this->be(User::find(2));

        $this->call('GET', '/matter')
            ->assertStatus(200)
            ->assertSeeText('PAT001');

        $this->call('GET', '/matter/4')->assertStatus(200);
    }
}
