# Onboarding Implementation för Nya Kunder

## Översikt

Detta dokument beskriver implementeringen av automatiserad onboarding för nya organisationer/kunder i SIMS (GDPR Management System). Onboarding-funktionen hjälper auditorer att snabbt konfigurera en ny organisation med relevanta uppgifter och mallar baserat på organisationens profil.

## Komponenter

### 1. Databasmigrationer

#### `2025_01_15_100000_create_template_tasks_table.php`
- Skapar tabellen `template_tasks` som innehåller förkonfigurerade uppgifter för varje mall
- Viktiga fält:
  - `template_id`: Koppling till mallen
  - `title_en/se`: Uppgiftens titel på engelska/svenska
  - `desc_en/se`: Beskrivning av uppgiften
  - `offset_days`: Antal dagar från organisationens skapandedatum till uppgiftens start
  - `duration_days`: Hur lång tid uppgiften ska ta (i dagar)
  - `hours`: Uppskattade timmar för att slutföra uppgiften
  - `action_type`: Plan, Do eller Review
  - `task_status_id`: Initial status för uppgiften

#### `2025_01_15_100001_add_organisation_id_to_tasks_table.php`
- Lägger till `organisation_id` i tasks-tabellen för att koppla uppgifter till organisationer

#### `2025_01_15_100002_add_metadata_to_templates_table.php`
- Utökar templates-tabellen med metadata för onboarding:
  - `organization_type`: Offentlig/Privat/Ideell
  - `organization_size`: Liten/Mellan/Stor
  - `requires_existing_gdpr`: Om befintligt dataskyddsarbete krävs
  - `summary_en/se`: Sammanfattning av mallens innehåll
  - `estimated_months`: Förväntad tid att bli klar
  - `is_active`: Om mallen är aktiv
  - `sort_order`: Sorteringsordning

### 2. Modeller

#### `TemplateTask.php`
- Modell för förkonfigurerade uppgifter i mallar
- Relationer: `template()`, `taskStatus()`

#### Uppdaterad `Template.php`
- Nya relationer: `templateTasks()`, `configs()`
- Scope: `active()` - filtrerar aktiva mallar
- Statisk metod: `getRecommended()` - hämtar rekommenderade mallar baserat på kriterier

#### Uppdaterad `Task.php` och `Organisation.php`
- Lagt till relation mellan tasks och organisationer

### 3. Controller

#### `OnboardingController.php`
Huvudcontroller för onboarding-funktionalitet med följande endpoints:

**`GET /{locale}/axios/onboarding/questions`**
- Returnerar frågor för onboarding-wizarden
- Frågor:
  1. Vilken typ av organisation?
  2. Hur stor är organisationen?
  3. Har ni befintligt dataskyddsarbete?

**`POST /{locale}/axios/onboarding/recommendations`**
- Body: `{ organization_type, organization_size, has_existing_gdpr }`
- Returnerar rekommenderade mallar baserat på svaren
- Inkluderar förhandsvisning av de första 5 uppgifterna i varje mall

**`POST /{locale}/axios/onboarding/organisations/{organisation}/apply-template`**
- Body: `{ template_id }`
- Applicerar vald mall på organisationen
- Skapar alla tasks från mallen med korrekta datum baserat på offset_days

**`POST /{locale}/axios/onboarding/complete`**
- Body: `{ organisation_id, template_id, answers }`
- Komplett onboarding i ett steg

#### Uppdaterad `AxiosController.php`
**`GET /{locale}/axios/templates`**
- Utökad att returnera fullständig information om mallar
- Inkluderar alla template_tasks med detaljer

### 4. Seeders

#### `TemplateSeeder.php`
Skapar tre förkonfigurerade mallar:

**1. Grundläggande GDPR-paket - Litet företag**
- 8 startuppgifter
- För privata, små företag utan befintligt GDPR-arbete
- Beräknad tid: 6 månader
- Uppgifter: Registerförteckning, DPO-utnämning, integritetspolicy, incidentrutin, utbildning, riskanalys, biträdesavtal, registreraderättigheter

**2. GDPR-paket för offentlig verksamhet**
- 5 huvuduppgifter
- För offentliga myndigheter
- Beräknad tid: 8 månader
- Extra fokus: Offentlighetsprincip, registrerades begäranden, PuL-övergång

**3. GDPR-paket för känsliga personuppgifter**
- 4 avancerade uppgifter
- För organisationer som behandlar känsliga uppgifter (t.ex. sjukvård)
- Kräver befintligt GDPR-arbete
- Beräknad tid: 10 månader
- Fokus: DPIA, förstärkta säkerhetsåtgärder, samtycke, åtkomstkontroll

## Användning

### För Backend-utvecklare

1. **Kör migrationer:**
```bash
php artisan migrate
```

2. **Kör seeders:**
```bash
php artisan db:seed --class=TemplateSeeder
```

3. **API-användning:**

```javascript
// Hämta frågor för wizard
GET /se/axios/onboarding/questions

// Få rekommendationer
POST /se/axios/onboarding/recommendations
{
  "organization_type": "private",
  "organization_size": "small",
  "has_existing_gdpr": false
}

// Applicera mall på organisation
POST /se/axios/onboarding/organisations/123/apply-template
{
  "template_id": 1
}
```

### För Frontend-utvecklare

**Onboarding-flöde:**

1. När en ny organisation skapas, visa onboarding-wizard
2. Hämta frågor från `/axios/onboarding/questions`
3. Visa frågorna till användaren
4. Skicka svaren till `/axios/onboarding/recommendations`
5. Visa rekommenderade mallar med:
   - Namn och beskrivning
   - Sammanfattning av innehåll
   - Antal uppgifter
   - Förhandsvisning av uppgifter
   - Beräknad tid
6. Låt användaren välja mall eller hoppa över
7. Applicera vald mall via `/axios/onboarding/organisations/{id}/apply-template`

**UI-komponenter som behövs:**
- Wizard-steg med frågor
- Mall-kort som visar innehåll
- Utfällbar lista med uppgifter
- Bekräftelse-dialog

### Manuell mallhantering

Administratörer kan:
- Skapa nya mallar via `/templates` (befintlig CRUD)
- Lägga till template_tasks för varje mall
- Aktivera/avaktivera mallar
- Sätta vilka organisationstyper/storlekar som passar

## Dataflöde

```
Organisation skapas
    ↓
Onboarding wizard visas
    ↓
Användare svarar på frågor
    ↓
System rekommenderar mallar
    ↓
Användare väljer mall
    ↓
Template tasks skapas som Tasks kopplade till organisation
    ↓
Organisation har nu färdiga uppgifter att jobba med
```

## Tekniska detaljer

### Task-skapande
När en mall appliceras:
1. Alla template_tasks hämtas för mallen
2. För varje template_task:
   - Startdatum = Nu + offset_days
   - Slutdatum = Startdatum + duration_days
   - Task skapas med organisation_id
   - created_by sätts till inloggad auditor

### Datumberäkning
```php
$startDate = Carbon::now()->addDays($templateTask->offset_days);
$endDate = $startDate->copy()->addDays($templateTask->duration_days);
```

### Mallfiltrering
Mallar filtreras baserat på:
- `organization_type`: Matchar eller NULL (passar alla)
- `organization_size`: Matchar eller NULL (passar alla)
- `requires_existing_gdpr`: Om false visas mallen för alla, om true endast för de med befintligt arbete
- `is_active`: Endast aktiva mallar visas

## Framtida förbättringar

1. **Påståenden-aktivering**: Möjlighet att markera vissa påståenden som ej tillämpliga baserat på mall
2. **SNI-baserad filtrering**: Automatisk sanktionsfiltrering baserat på SNI-kod
3. **Återkommande uppgifter**: Schema för kvartals-/årliga uppföljningar
4. **Mall-versioner**: Versionshantering av mallar
5. **Anpassningsbara mallar**: Låt användare justera mallar innan applicering
6. **Progress tracking**: Visa framsteg i onboarding-processen
7. **Mall-export/import**: Dela mallar mellan system

## Säkerhet

- Endast auditorer kan applicera mallar (`can:auditor`)
- Transactions används för att säkerställa dataintegitet
- Loggning av vilken mall som applicerats och av vem
- Validering av alla inputs

## Testning

För att testa funktionen:

1. Skapa en ny organisation
2. Anropa onboarding-endpoints med olika svar
3. Verifiera att rätt mallar rekommenderas
4. Applicera en mall
5. Kontrollera att tasks skapats korrekt med rätt datum
6. Verifiera att organisation_id är satt på alla tasks

## Support och frågor

För frågor kontakta utvecklingsteamet eller se kodkommentarer i:
- `app/Http/Controllers/OnboardingController.php`
- `app/Models/Template.php`
- `app/Models/TemplateTask.php`
