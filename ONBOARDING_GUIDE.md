# Onboarding Guide - Så här använder du onboarding-funktionen

## Åtkomst till Onboarding

**URL:** `http://127.0.0.1:8000/se/onboarding`

**Krav:** Du måste vara inloggad som **Auditor** för att få tillgång till onboarding-sidan.

**Login-uppgifter:**
- Email: `auditor@test.example`
- Lösenord: `password123`

## Hur man använder Onboarding Wizard

Onboarding-processen består av 3 steg:

### Steg 1: Besvara frågor

1. **Organisationstyp:** Välj mellan:
   - Offentlig myndighet
   - Privat bolag
   - Ideell organisation
   - Annat

2. **Organisationsstorlek:**
   - Liten (1-50 anställda)
   - Mellan (51-250 anställda)
   - Stor (250+ anställda)

3. **Befintligt dataskyddsarbete:**
   - Ja (för organisationer med befintliga GDPR-rutiner)
   - Nej (för nya organisationer)

4. **Välj organisation:**
   - Välj vilken organisation du vill konfigurera onboarding för

5. Klicka på **"Nästa"** för att få rekommendationer

### Steg 2: Välj mall

Baserat på dina svar visar systemet rekommenderade mallar:

**Tillgängliga mallar:**

1. **Grundläggande GDPR-paket - Litet företag**
   - För små privata företag
   - 8 startuppgifter
   - 6 månaders beräknad tid
   - Täcker: Registerförteckning, DPO, Integritetspolicy, Incidentrutin, Utbildning, Riskanalys, Biträdesavtal, Registreraderättigheter

2. **GDPR-paket för offentlig verksamhet**
   - För offentliga myndigheter
   - 5 huvuduppgifter
   - 8 månaders beräknad tid
   - Extra fokus: Offentlighetsprincip, Registrerades begäranden

3. **GDPR-paket för känsliga personuppgifter**
   - För organisationer som hanterar känsliga uppgifter (t.ex. sjukvård)
   - 4 avancerade uppgifter
   - 10 månaders beräknad tid
   - Fokus: DPIA, Säkerhetsåtgärder, Samtycke, Åtkomstkontroll

**För varje mall ser du:**
- Namn och beskrivning
- Sammanfattning av innehåll
- Antal uppgifter som kommer skapas
- Beräknad tid att slutföra
- Förhandsvisning av de första 5 uppgifterna

Klicka på en mall för att välja den, sedan **"Nästa"**

### Steg 3: Bekräfta och applicera

Här ser du en sammanfattning:
- Vald organisation
- Vald mall
- Antal uppgifter som kommer skapas
- Detaljer om uppgifterna (titel, beskrivning, start, längd)

Klicka på **"Applicera mall"** för att:
- Skapa alla uppgifter från mallen
- Koppla dem till organisationen
- Sätta korrekta start- och slutdatum

## Vad händer efter applicering?

När du applicerar en mall:

1. **Tasks skapas automatiskt**
   - Alla uppgifter från mallen skapas i databasen
   - Varje task kopplas till den valda organisationen
   - Start- och slutdatum beräknas från dagens datum
   - Status sätts till "Ej påbörjad"

2. **Datumberäkning**
   - Startdatum = Idag + offset_days (från template_task)
   - Slutdatum = Startdatum + duration_days (från template_task)

3. **Exempel:**
   - Om en uppgift har `offset_days: 7` och `duration_days: 30`
   - Startar om 7 dagar från idag
   - Slutar 30 dagar efter start (37 dagar från idag)

4. **Resultat**
   - Du får ett bekräftelsemeddelande med antal skapade tasks
   - Du kan sedan se och hantera dessa uppgifter i organisationens tasks-vy

## Vanliga användningsfall

### Fall 1: Nytt litet privat företag utan GDPR-arbete
**Svar:**
- Typ: Privat bolag
- Storlek: Liten
- Befintligt GDPR: Nej

**Rekommenderad mall:** Grundläggande GDPR-paket
**Resultat:** 8 grundläggande uppgifter för att komma igång

### Fall 2: Offentlig myndighet
**Svar:**
- Typ: Offentlig myndighet
- Storlek: Mellan/Stor
- Befintligt GDPR: Nej/Ja

**Rekommenderad mall:** GDPR-paket för offentlig verksamhet
**Resultat:** 5 uppgifter fokuserade på offentlighetsprincip

### Fall 3: Sjukvård eller organisationer med känsliga uppgifter
**Svar:**
- Typ: Privat bolag/Offentlig
- Storlek: Valfri
- Befintligt GDPR: Ja (rekommenderat)

**Rekommenderad mall:** GDPR-paket för känsliga personuppgifter
**Resultat:** 4 avancerade uppgifter för höga säkerhetskrav

## Tekniska detaljer

### API-endpoints som används

Onboarding-sidan använder följande endpoints:

1. **GET** `/se/axios/onboarding/questions` - Hämtar frågor
2. **POST** `/se/axios/onboarding/recommendations` - Får mallrekommendationer
3. **GET** `/se/axios/templates` - Hämtar alla mallar med detaljer
4. **POST** `/se/axios/onboarding/organisations/{id}/apply-template` - Applicerar mall

### Frontend-ramverk
- **BS Stepper** för wizard-funktionalitet
- **Feather Icons** för ikoner
- **Bootstrap 5** för styling
- **Vanilla JavaScript** för interaktivitet

### Backend-logik
- Templates filtreras baserat på:
  - `organization_type` (matchar eller NULL)
  - `organization_size` (matchar eller NULL)
  - `requires_existing_gdpr` (true/false)
  - `is_active` (endast aktiva mallar)

## Troubleshooting

**Problem:** "Inga mallar hittades"
- **Lösning:** Kontrollera att templates finns i databasen (`php artisan db:seed --class=TemplateSeeder`)

**Problem:** "Fel vid applicering av mall"
- **Lösning:** Kontrollera att organisation_id är korrekt och att användaren har rätt behörigheter

**Problem:** "Organisations-dropdown är tom"
- **Lösning:** Se till att det finns organisationer i databasen som användaren har tillgång till

**Problem:** Wizard går inte vidare
- **Lösning:** Fyll i alla obligatoriska fält innan du klickar "Nästa"

## Nästa steg efter onboarding

Efter att ha applicerat en mall kan du:
1. Se de skapade uppgifterna i organisationens tasks-vy
2. Redigera, ta bort eller lägga till fler uppgifter manuellt
3. Tilldela uppgifter till specifika användare
4. Följa framsteg genom att uppdatera task-status

## Support

Vid problem eller frågor, se:
- [ONBOARDING_IMPLEMENTATION.md](ONBOARDING_IMPLEMENTATION.md) - Teknisk dokumentation
- Kontakta utvecklingsteamet
