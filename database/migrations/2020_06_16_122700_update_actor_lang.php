<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActorLang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actor', function (Blueprint $table) {
            if ( !Schema::hasColumn('actor', 'language')) {
                $table->char('language', 2)->nullable()->after('nationality')->default(NULL);
            }
        });
        $id = DB::table('template_classes')->insertGetId([
          'name' => 'sys_renewals',
          'notes' => 'Templates used for the renewal management tool',
          'creator' => 'system',
          'updater' => 'system'
        ]);
        DB::table('template_members')->insert([
          'class_id' => $id,
          'language' => 'fr',
          'category' => 'firstcall',
          'format' => 'HTML',
          'summary' => "Annuités - premier appel",
          'subject' => "Prochaines taxes de maintien en vigueur de vos titres",
          'body' => "<p>Veuillez trouver ci-joint une liste de titres dont le renouvellement arrive à échéance prochainement. Je vous remercie de me transmettre vos instructions accompagnées du règlement correspondant, de préférence un mois avant l'échéance.</p>",
          'creator' => 'system',
          'updater' => 'system'
        ]);
        DB::table('template_members')->insert([
          'class_id' => $id,
          'language' => 'fr',
          'category' => 'warncall',
          'format' => 'HTML',
          'summary' => "Annuités - rappel urgent",
          'subject' => "Prochaines taxes de maintien en vigueur de vos titres",
          'body' => "<p>Nous n'avons pas reçu vos instructions concernant le maintien ou non des titres cités ci-dessous dans le délai normal de paiement, qui est maintenant dépassé. Sans instructions, nous ne ne procéderons à aucun renouvellement. Les renouvellements peuvent encore être payés moyennant une surtaxe incluse dans le tableau ci-dessous. Nous vous remercions de nous transmettre vos instructions accompagnées du règlement correspondant.</p>",
          'creator' => 'system',
          'updater' => 'system'
        ]);
        DB::table('template_members')->insert([
          'class_id' => $id,
          'language' => 'fr',
          'category' => 'lastcall',
          'format' => 'HTML',
          'summary' => "Annuités - dernir rappel",
          'subject' => "[DERNIER RAPPEL] Prochaines taxes de maintien en vigueur de vos titres",
          'body' => "<p>Nous n'avons pas encore reçu vos instructions concernant le maintien ou non des titres cités ci-dessous. Sans instructions, nous ne procéderons à aucun renouvellement. Passée la date d'échéance, les renouvellements pourront encore être payés moyennant une surtaxe. Je vous remercie de me transmettre EN RETOUR vos instructions accompagnées du règlement correspondant.</p>",
          'creator' => 'system',
          'updater' => 'system'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actor', function (Blueprint $table) {
            if ( Schema::hasColumn('actor', 'language')) {
                $table->dropColumn('language');
            }
        });
    }
}
