<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateTask;
use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get default task status (assume status ID 1 is "Ej påbörjad" / "Not started")
        $defaultStatus = TaskStatus::first();

        // Template 1: Grundläggande GDPR-paket för små företag
        $template1 = Template::create([
            'name_en' => 'Basic GDPR Package - Small Business',
            'name_se' => 'Grundläggande GDPR-paket - Litet företag',
            'desc_en' => 'Comprehensive starter package for small businesses with minimal existing data protection work.',
            'desc_se' => 'Komplett startpaket för små företag med minimalt befintligt dataskyddsarbete.',
            'summary_en' => 'Includes 8 initial tasks covering record of processing activities, policy development, staff training, and risk assessment. Plus 12 quarterly follow-up checks.',
            'summary_se' => 'Innehåller 8 startuppgifter som täcker registerförteckning, policyutveckling, personalutbildning och riskanalys. Plus 12 kvartalsvisa uppföljningskontroller.',
            'organization_type' => 'private',
            'organization_size' => 'small',
            'requires_existing_gdpr' => false,
            'estimated_months' => 6,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Tasks for Template 1
        $tasks1 = [
            [
                'title_en' => 'Establish Record of Processing Activities',
                'title_se' => 'Upprätta registerförteckning',
                'desc_en' => 'Document all personal data processing activities in the organization according to GDPR Article 30.',
                'desc_se' => 'Dokumentera alla personuppgiftsbehandlingar i organisationen enligt GDPR artikel 30.',
                'offset_days' => 7,
                'duration_days' => 30,
                'hours' => 8.0,
                'action_type' => 'plan',
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Appoint Data Protection Officer (if required)',
                'title_se' => 'Utse dataskyddsombud (om erforderligt)',
                'desc_en' => 'Determine if a DPO is required and appoint one if necessary.',
                'desc_se' => 'Avgör om ett dataskyddsombud krävs och utse ett om nödvändigt.',
                'offset_days' => 7,
                'duration_days' => 14,
                'hours' => 4.0,
                'action_type' => 'plan',
                'sort_order' => 2,
            ],
            [
                'title_en' => 'Create Privacy Policy',
                'title_se' => 'Skapa integritetspolicy',
                'desc_en' => 'Develop a comprehensive privacy policy for employees and customers.',
                'desc_se' => 'Ta fram en omfattande integritetspolicy för anställda och kunder.',
                'offset_days' => 30,
                'duration_days' => 21,
                'hours' => 6.0,
                'action_type' => 'do',
                'sort_order' => 3,
            ],
            [
                'title_en' => 'Develop Incident Response Procedure',
                'title_se' => 'Upprätta incidenthanteringsrutin',
                'desc_en' => 'Create procedures for handling personal data breaches and incidents.',
                'desc_se' => 'Skapa rutiner för hantering av personuppgiftsincidenter.',
                'offset_days' => 45,
                'duration_days' => 21,
                'hours' => 5.0,
                'action_type' => 'plan',
                'sort_order' => 4,
            ],
            [
                'title_en' => 'Conduct GDPR Training for Staff',
                'title_se' => 'Genomför GDPR-utbildning för personal',
                'desc_en' => 'Provide basic GDPR training to all employees handling personal data.',
                'desc_se' => 'Ge grundläggande GDPR-utbildning till all personal som hanterar personuppgifter.',
                'offset_days' => 60,
                'duration_days' => 30,
                'hours' => 4.0,
                'action_type' => 'do',
                'sort_order' => 5,
            ],
            [
                'title_en' => 'Perform Initial Risk Assessment',
                'title_se' => 'Gör en första riskanalys',
                'desc_en' => 'Conduct a data protection impact assessment for high-risk processing activities.',
                'desc_se' => 'Genomför en konsekvensbedömning för högriskbehandlingar.',
                'offset_days' => 90,
                'duration_days' => 30,
                'hours' => 10.0,
                'action_type' => 'review',
                'sort_order' => 6,
            ],
            [
                'title_en' => 'Review Data Processor Agreements',
                'title_se' => 'Granska personuppgiftsbiträdesavtal',
                'desc_en' => 'Ensure all data processors have proper GDPR-compliant agreements in place.',
                'desc_se' => 'Säkerställ att alla personuppgiftsbiträden har korrekta GDPR-kompatibla avtal.',
                'offset_days' => 120,
                'duration_days' => 30,
                'hours' => 6.0,
                'action_type' => 'review',
                'sort_order' => 7,
            ],
            [
                'title_en' => 'Implement Data Subject Rights Procedures',
                'title_se' => 'Implementera rutiner för registrerades rättigheter',
                'desc_en' => 'Establish processes for handling data subject access requests and other rights.',
                'desc_se' => 'Upprätta processer för att hantera registerutdrag och andra registreraderättigheter.',
                'offset_days' => 150,
                'duration_days' => 21,
                'hours' => 5.0,
                'action_type' => 'plan',
                'sort_order' => 8,
            ],
        ];

        foreach ($tasks1 as $taskData) {
            TemplateTask::create(array_merge($taskData, [
                'template_id' => $template1->id,
                'task_status_id' => $defaultStatus->id,
            ]));
        }

        // Template 2: Offentlig verksamhet
        $template2 = Template::create([
            'name_en' => 'Public Sector GDPR Package',
            'name_se' => 'GDPR-paket för offentlig verksamhet',
            'desc_en' => 'Specialized package for public authorities with additional requirements around transparency and public access.',
            'desc_se' => 'Specialiserat paket för offentliga myndigheter med extra krav kring transparens och offentlighetsprincip.',
            'summary_en' => 'Includes 10 tasks covering basic GDPR compliance plus public sector specific requirements like freedom of information and PuL transition considerations.',
            'summary_se' => 'Innehåller 10 uppgifter som täcker grundläggande GDPR-efterlevnad plus offentliga sektorns specifika krav som offentlighetsprincip och PuL-övergång.',
            'organization_type' => 'public',
            'organization_size' => null,
            'requires_existing_gdpr' => false,
            'estimated_months' => 8,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $tasks2 = [
            [
                'title_en' => 'Establish Record of Processing Activities',
                'title_se' => 'Upprätta registerförteckning',
                'desc_en' => 'Document all personal data processing activities according to GDPR Article 30.',
                'desc_se' => 'Dokumentera alla personuppgiftsbehandlingar enligt GDPR artikel 30.',
                'offset_days' => 7,
                'duration_days' => 30,
                'hours' => 10.0,
                'action_type' => 'plan',
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Appoint Data Protection Officer',
                'title_se' => 'Utse dataskyddsombud',
                'desc_en' => 'Appoint a DPO as required for public authorities under GDPR.',
                'desc_se' => 'Utse ett dataskyddsombud som krävs för offentliga myndigheter enligt GDPR.',
                'offset_days' => 7,
                'duration_days' => 14,
                'hours' => 4.0,
                'action_type' => 'plan',
                'sort_order' => 2,
            ],
            [
                'title_en' => 'Review Public Access Principles',
                'title_se' => 'Granska offentlighetsprincip',
                'desc_en' => 'Ensure GDPR compliance while maintaining public access to documents.',
                'desc_se' => 'Säkerställ GDPR-efterlevnad samtidigt som offentlighetsprincipen upprätthålls.',
                'offset_days' => 30,
                'duration_days' => 21,
                'hours' => 8.0,
                'action_type' => 'review',
                'sort_order' => 3,
            ],
            [
                'title_en' => 'Handle Data Subject Rights Requests',
                'title_se' => 'Hantera registrerades begäranden',
                'desc_en' => 'Establish procedures for handling access requests and other data subject rights.',
                'desc_se' => 'Upprätta rutiner för att hantera registerutdrag och andra registreraderättigheter.',
                'offset_days' => 60,
                'duration_days' => 30,
                'hours' => 6.0,
                'action_type' => 'plan',
                'sort_order' => 4,
            ],
            [
                'title_en' => 'Create Privacy Policy',
                'title_se' => 'Skapa integritetspolicy',
                'desc_en' => 'Develop comprehensive privacy policy for citizens and employees.',
                'desc_se' => 'Ta fram omfattande integritetspolicy för medborgare och anställda.',
                'offset_days' => 90,
                'duration_days' => 30,
                'hours' => 8.0,
                'action_type' => 'do',
                'sort_order' => 5,
            ],
        ];

        foreach ($tasks2 as $taskData) {
            TemplateTask::create(array_merge($taskData, [
                'template_id' => $template2->id,
                'task_status_id' => $defaultStatus->id,
            ]));
        }

        // Template 3: Känsliga personuppgifter (t.ex. sjukvård)
        $template3 = Template::create([
            'name_en' => 'Sensitive Data GDPR Package',
            'name_se' => 'GDPR-paket för känsliga personuppgifter',
            'desc_en' => 'Enhanced package for organizations processing sensitive personal data such as healthcare, with additional security and compliance requirements.',
            'desc_se' => 'Utökat paket för organisationer som behandlar känsliga personuppgifter såsom sjukvård, med extra säkerhets- och efterlevnadskrav.',
            'summary_en' => 'Includes 12 tasks focusing on heightened security measures, data protection impact assessments, and strict consent management for sensitive data.',
            'summary_se' => 'Innehåller 12 uppgifter med fokus på höjda säkerhetsåtgärder, konsekvensbedömningar och strikt samtyckes­hantering för känsliga uppgifter.',
            'organization_type' => null,
            'organization_size' => null,
            'requires_existing_gdpr' => true,
            'estimated_months' => 10,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $tasks3 = [
            [
                'title_en' => 'Conduct DPIA for Sensitive Data Processing',
                'title_se' => 'Genomför konsekvensbedömning för känsliga uppgifter',
                'desc_en' => 'Perform comprehensive Data Protection Impact Assessment for all sensitive data processing.',
                'desc_se' => 'Genomför omfattande konsekvensbedömning för all behandling av känsliga personuppgifter.',
                'offset_days' => 7,
                'duration_days' => 45,
                'hours' => 20.0,
                'action_type' => 'review',
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Implement Enhanced Security Measures',
                'title_se' => 'Implementera förstärkta säkerhetsåtgärder',
                'desc_en' => 'Deploy additional technical and organizational security measures for sensitive data.',
                'desc_se' => 'Införa ytterligare tekniska och organisatoriska säkerhetsåtgärder för känsliga uppgifter.',
                'offset_days' => 30,
                'duration_days' => 60,
                'hours' => 30.0,
                'action_type' => 'do',
                'sort_order' => 2,
            ],
            [
                'title_en' => 'Review Consent Management',
                'title_se' => 'Granska samtyckeshantering',
                'desc_en' => 'Ensure proper consent collection and management for sensitive data processing.',
                'desc_se' => 'Säkerställ korrekt inhämtning och hantering av samtycke för känsliga uppgifter.',
                'offset_days' => 60,
                'duration_days' => 30,
                'hours' => 12.0,
                'action_type' => 'review',
                'sort_order' => 3,
            ],
            [
                'title_en' => 'Establish Access Control Protocols',
                'title_se' => 'Upprätta åtkomstkontrollprotokoll',
                'desc_en' => 'Implement strict access controls and logging for sensitive data.',
                'desc_se' => 'Implementera strikt åtkomstkontroll och loggning för känsliga uppgifter.',
                'offset_days' => 90,
                'duration_days' => 30,
                'hours' => 15.0,
                'action_type' => 'do',
                'sort_order' => 4,
            ],
        ];

        foreach ($tasks3 as $taskData) {
            TemplateTask::create(array_merge($taskData, [
                'template_id' => $template3->id,
                'task_status_id' => $defaultStatus->id,
            ]));
        }

        $this->command->info('Templates and template tasks seeded successfully!');
    }
}
