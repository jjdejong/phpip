<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TemplateMembersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('template_members')->insertOrIgnore(array(

            array(
                'id' => 1,
                'class_id' => 1,
                'language' => 'fr',
                'style' => null,
                'category' => 'firstcall',
                'format' => 'HTML',
                'summary' => 'Annuités - premier appel',
                'subject' => 'Prochaines taxes de maintien en vigueur de vos titres',
                'body' => '<p>Veuillez trouver ci-joint une liste de titres dont le renouvellement arrive à échéance prochainement. Je vous remercie de me transmettre vos instructions accompagnées du règlement correspondant, de préférence un mois avant l\'échéance.</p>',
                'creator' => 'system',
                'updater' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 2,
                'class_id' => 1,
                'language' => 'fr',
                'style' => null,
                'category' => 'warncall',
                'format' => 'HTML',
                'summary' => 'Annuités - avertissement',
                'subject' => 'Prochaines taxes de maintien en vigueur de vos titres',
                'body' => '<p>Nous n\'avons pas reçu vos instructions concernant le maintien ou non des titres cités ci-dessous dans le délai normal de paiement, qui est maintenant dépassé. Sans instructions, nous ne ne procéderons à aucun renouvellement. Les renouvellements peuvent encore être payés moyennant une surtaxe incluse dans le tableau ci-dessous. Nous vous remercions de nous transmettre vos instructions accompagnées du règlement correspondant.</p>',
                'creator' => 'system',
                'updater' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 3,
                'class_id' => 1,
                'language' => 'fr',
                'style' => null,
                'category' => 'lastcall',
                'format' => 'HTML',
                'summary' => 'Annuités - dernier rappel',
                'subject' => '[DERNIER RAPPEL] Prochaines taxes de maintien en vigueur de vos titres',
                'body' => '<p>Nous n\'avons pas encore reçu vos instructions concernant le maintien ou non des titres cités ci-dessous. Sans instructions, nous ne procéderons à aucun renouvellement. Passée la date d\'échéance, les renouvellements pourront encore être payés moyennant une surtaxe. Je vous remercie de me transmettre EN RETOUR vos instructions accompagnées du règlement correspondant.</p>',
                'creator' => 'system',
                'updater' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
