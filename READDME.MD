# Schritt-für-Schritt Plan: ina.YOGA Website

Mobile-first responsive HTML-Website für die Yoga-Website von Ina. Ziel: User sehen Events, Informationen über Yoga-Angebote und haben Kontaktmöglichkeiten.

---

## Phase 1: Grundgerüst & Technische Basis

### Schritt 1: HTML-Grundstruktur
- HTML5-Dokument mit semantischen Tags (`<header>`, `<main>`, `<section>`, `<footer>`)
- Meta-Tags für Viewport und Responsiveness
- Adobe Typekit einbinden: `https://use.typekit.net/fmg7cfm.css`
- Fonts: **Audrielle No 2** (Script/Überschriften), **Contralto Big** (Akzente), **Montserrat Light** (Fließtext, Buttons)

### Schritt 2: Farbpalette (aus Briefing)
- CSS-Variablen für alle Farben:
  - Lavender: `#e0d8eb`
  - Light Yellow: `#f3f7af`
  - Light Green: `#bdfbb6`
  - Light Pink: `#eedefa`
  - Light Blue: `#b0dbe0`
  - Light Coral: `#ffdbb1`
  - Dark Grey/Teal: `#67716f`
  - Off-white: `#f7f8f6`

### Schritt 3: Ordnerstruktur
- `images/` – alle Bilder
- `Buttons/` – Button-Grafiken
- `Icons/` – Icons (Social, Pfeile, Menu)
- Wavy Dividers – per SVG/CSS oder vorhandene Assets

### Schritt 3b: Typografie- und Stil-Richtlinien (für User-Orientierung)
- **Überschriften (H1, Sektionstitel):** Audrielle No 2 (Script), elegant, persönlich
- **Untertitel / Akzente:** Contralto Big
- **Fließtext, Buttons, Listen:** Montserrat Light – einheitlich über gesamte Website
- **Ziel:** User findet sich schnell zurecht, klare Hierarchie, konsistenter Look

### Schritt 3c: Bauprinzip Mobile-First + Responsive
- **Basis:** Mobile (ab 320px) zuerst bauen
- **Breakpoints:** Tablet 768px, Desktop 1024px – sofort mit einbauen
- **Regel:** Jede Sektion wird **von Anfang an** mobile-first UND responsive gebaut
- **Fertig = fertig:** Wenn eine Sektion fertig ist, muss sie nicht mehr angefasst werden – keine Nacharbeit

---

## Phase 2: Globaler Sticky Header

### Schritt 4: Header-Layout
- Sticky Header (`position: sticky`) über dem gesamten Inhalt
- Links: Logo
- Mitte: Social-Icons (WhatsApp, Instagram, Mail) – Links aus Briefing
- Rechts: Burger-Menü-Icon (`Icons/Menu Black.png`)
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 5: Burger-Menü
- **Mobile:** Klick öffnet Overlay/Dropdown mit Navigation
- **Desktop:** Burger wird durch horizontale Navigation ersetzt
- Menüpunkte: Über mich | Yoga Kurse | Firmenyoga | Sound Bath | Nuad Thai | Events | Blog

### Schritt 6: Social-Links
- WhatsApp: `https://wa.me/4369919112175`
- Instagram: `https://www.instagram.com/iina.yoga/`
- Mail: `mailto:corina.ung@gmx.at`

---

## Phase 3: Hero Section

### Schritt 7: Hero-Aufbau
- **Bild:** `images/Hero_Image_Ina_Mobile.png` (Mobile), Desktop-Variante optional
- **Dekoration:** `Icons/Accent_Yellow_Image_Ina.png` oben rechts
- **Headline (Script, zentriert):** „be true. be you.“
- **Button:** `Button_Yellow_1_Images_Ina.png` | Text: „Finde deinen Kurs“ | Schrift: Montserrat Light, uppercase | Ziel: Anker #yoga-kurse
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 8: Willkommenstext + Wavy Divider
- **Einbettung:** Kurzer Absatz auf grauem Hintergrund (#67716f), Montserrat Light
- **Text (aus Webdesign):** Willkommenstext, der User einlädt und Orientierung gibt – z.B. „Bewegung, die verbindet. Ruhe, die wirkt.“ oder persönlicher Einstieg
- **Wavy Divider am Ende der Sektion** (Yellow→Violet) – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 4: Meine Angebote

### Schritt 9: Angebote-Übersicht + Wavy Divider
- **Bilder:** `images/Übersicht_1_Image_Ina.png` bis `Übersicht_4_Image_Ina.png` (2×2-Grid)
- **Überschrift (Script):** „MEINE ANGEBOTE“
- **Link-Text (Montserrat Light):** „KLICKE FÜR MEHR“ mit `Icons/Pfeil_1_Image_Ina.png`
- Hintergrund: Light Yellow (#f3f7af)
- **Wavy Divider am Ende** – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 10: Yoga-Angebot – Fullscreen Popup-Modal
- **Öffnung:** Klick auf Yoga-Kachel in „meine angebote“
- **Layout:** Fullscreen-Modal mit Bild und Beschreibung
- **Bild:** `images/Angebote_1_Image_Ina.png`
- **Überschrift (Script):** „Yoga“ + „mit Ina“
- **Fließtext (Montserrat Light, Blocksatz):** Beschreibung von Vinyasa Flow, Gentle Yoga, Pranayama, Meditation, Yoga nidra, Workshops – Betonung auf ruhige und dynamische Klassen, Hatha, Vinyasa, Yin, Einzelstunden
- **Buttons:** 3× `Button_Yellow_1_Images_Ina.png` | Text: YOGA-KURSE | SPECIAL EVENTS | FIRMENYOGA | Schrift: Montserrat Light, uppercase | Ziel: Anker #yoga-kurse, Anker #events, Link Firmenyoga-Seite
- **Link-Text:** „WAS DICH ERWARTET“ mit `Icons/Pfeile_violet.png` – öffnet Schritt-11-Modal
- **Schließen:** X-Button oder Klick außerhalb
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 11: „Was dich erwartet“ – Yoga – Fullscreen Popup-Modal
- **Öffnung:** Klick auf „WAS DICH ERWARTET“ im Yoga-Modal (Schritt 10)
- **Layout:** Fullscreen-Modal mit Bild und Beschreibung
- **Bild:** `images/WasDichErwartet_1_Image_Ina.png`
- **Überschrift (Script):** „Yoga“ + „mit Ina“
- **Fließtext (Montserrat Light):** Detaillierte Beschreibung, was Yoga-Stunden mit Ina ausmacht – Ausführung, Atmosphäre, individuelle Anpassung
- **Button:** `Button_Violet_Images_Ina.png` | Text: YOGA-KURSE | Schrift: Montserrat Light, uppercase | Ziel: Anker #yoga-kurse
- **Schließen:** X-Button oder Klick außerhalb
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 12: Sound Bath-Angebot – Fullscreen Popup-Modal
- **Öffnung:** Klick auf Sound-Bath-Kachel in „meine angebote“
- **Layout:** Fullscreen-Modal mit Bild und Beschreibung
- **Bild:** `images/Angebote_2_Image_Ina.png`
- **Überschrift (Script):** „Sound Bath“
- **Fließtext (Montserrat Light):** „Wohltuende Klangreise“ für Körper, Geist und Seele – Klangschalen, Gongs, Koshi-Klangspiele; Stressabbau, bessere Schlafqualität, Tiefenentspannung
- **Link-Text:** „WAS DICH ERWARTET“ mit `Icons/Pfeile_yellow.png` – öffnet optionales Detail-Modal mit `images/WasDichErwartet_2_Image_Ina.png`
- **Button:** `Button_Yellow_1_Images_Ina.png` | Text: SOUND BATH ANGEBOT | Schrift: Montserrat Light, uppercase | Ziel: Anker #sound-bath
- **Schließen:** X-Button oder Klick außerhalb
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 13: Nuad Thai Massage-Angebot – Fullscreen Popup-Modal
- **Öffnung:** Klick auf Nuad-Thai-Kachel in „meine angebote“
- **Layout:** Fullscreen-Modal mit Bild und Beschreibung
- **Bild:** `images/Angebote_3_Image_Ina.png`
- **Überschrift (Script):** „Nadi Thai Massage“ oder „Nuad Thai Massage“
- **Fließtext (Montserrat Light):** „Berührt und tiefenwirksam“ – sanfte Dehnungen, Akupressur, Energiearbeit; Blockaden lösen, Energiefluss, tiefe Entspannung
- **Link-Text:** „WAS DICH ERWARTET“ mit blauen Pfeilen – öffnet optionales Detail-Modal mit `images/WasDichErwartet_3_Image_Ina.png`
- **Button:** `Button_BlueBlack_Images_Ina.png` | Text: NUAD-ANGEBOT | Schrift: Montserrat Light, uppercase | Ziel: Anker #nuad-thai
- **Schließen:** X-Button oder Klick außerhalb
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 5: Yoga Kurse & Preise

### Schritt 14: Yoga-Kurse-Sektion + Wavy Divider
- **Bild/Icon:** `Icons/LightBulb_Violet_Image_Ina.png`; optional `images/Ausgleich_Image_Ina.png` (falls passend)
- **Überschrift (vertikal, Script):** „YOGA KURSE“
- **Fließtext (Montserrat Light, Blocksatz):** Persönliches Wachstum und Balance durch Yoga – Text zur Kursphilosophie
- **Button:** `Button_Yellow_1_Images_Ina.png` | Text: KONTAKTIERE MICH | Schrift: Montserrat Light, uppercase | Ziel: Anker #kontakt
- **Wavy Divider am Ende** – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 15: Kursübersicht & Preise
- **Kurskarten (aus Webdesign):** „Anfängerkurs“ (Sanftes Yoga, MO: 18:30) | „Aufbaukurs“ (Fließendes Yoga, DI: 19:00) – oder Hatha/Vinyasa, Online Donnerstag
- **Preiskarte (Montserrat Light):** Schnupperstunde 15€ | Einzelstunde 17€ | 5er Block 80€ | 10er Block 150€
- **Link-Text:** „Weitere Informationen zu den Kursen findest du hier.“
- **Abschluss (Script):** „Ich freue mich auf dich!“
- **Button:** `Button_Violet_Images_Ina.png` | Text: KONTAKT AUFNEHMEN oder BUCHE DEINE STUNDE | Schrift: Montserrat Light, uppercase | Ziel: Anker #kontakt
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 6: Events

### Schritt 16: Events-Sektion + Wavy Divider
- **Überschrift (Script):** „EVENTS“
- **Mobile:** Eventboxen untereinander
- **Desktop:** Slideshow mit Wechsel alle 1,5 Sekunden
- **Navigation:** `Icons/PfeilLinks_White.png`, `Icons/PfeilRechts_White.png`
- **Wavy Divider am Ende** – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen** (Liste Mobile, Slideshow Desktop)

### Schritt 17: Eventboxen
- **Bilder:** `images/Frühstück_Image_Ina.png`, `images/Retreat_Image_Ina.png` (ggf. `images/Ausgleich_Image_Ina.png` für 3. Event)
- **Event 1 – Yoga Brunch:** Überschrift „YOGA BRUNCH“ | Text: Tiefenentspannung, Naturerlebnis | Datum, Uhrzeit, Ort, Preis
- **Event 2 – Sound Bath:** Überschrift „SOUND BATH“ / „KLANGREISE“ | Text: Innere Harmonie, Tiefenentspannung | Datum, Uhrzeit, Ort, Preis
- **Event 3 – One Night Retreat:** Überschrift „ONE NIGHT YOGA & KLANGREISE“ | Text: Klangbad, Wohlfühlatmosphäre | Datum, Ort, Preis (z.B. ab 250€)
- **Button (Eventbox):** `Button_Green_Images_Ina.png` | Text: MEHR DAZU | Schrift: Montserrat Light, uppercase | Ziel: öffnet Pop-up
- **Button (Pop-up):** `Button_Green_Images_Ina.png` | Text: RESERVIEREN | Schrift: Montserrat Light, uppercase | Ziel: Anker #kontakt
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 18: Nuad Thai Detail-Sektion + Wavy Divider
- **Icons:** `Icons/Nuad_Massage_Image_Ina.png`, `Icons/LightBulb_Blue_Image_Ina.png`
- **Überschrift (Script):** „NUAD THAI MASSAGE“
- **Fließtext (Montserrat Light):** „Berührt und tiefenwirksam“ – sanfte Dehnungen, Akupressur, Energiearbeit; Dauer: 75 Min., Preis: 90€
- **Button:** `Button_BlueBlack_Images_Ina.png` | Text: KONTAKTIERE MICH | Schrift: Montserrat Light, uppercase | Ziel: Anker #kontakt
- **Wavy Divider am Ende** – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 7: Testimonials

### Schritt 19: Teilnehmer-Stimmen + Wavy Divider
- **Überschrift (Script):** „TEILNEHMER STIMMEN“
- **Slider:** automatischer Wechsel alle 1,5 s; Pfeile: `Icons/PfeilLinks_White.png`, `Icons/PfeilRechts_White.png`
- **Testimonial 1 (aus Webdesign):** „Ina ist eine wahnsinnig wundervolle Yogalehrerin. In ihrer Stunde achtet sie auf eine gute Ausführung und ihre angenehme Stimme hilft durch stressige Situationen ruhig und gelassen zu bleiben.“ — Autor: Name ergänzen
- **Testimonial 2 (aus Webdesign):** „Deine aufrichtige, wärmende und achtsame Art ist wundervoll und bereichert mich durch dein ganzheitliches Yoga.“ — Anna S.
- **Testimonial 3 (aus Webdesign):** „Eine wundervolle und sehr entspannte Massage, die Körper und Geist in Einklang gebracht und genährt.“ — Autor: Name ergänzen
- **Testimonial 4 (Firmenkunde, aus Webdesign):** „Mit viel Fachwissen und Professionalität hat Ina unsere persönliche Gesundheitsreise optimal bereichert. Das Feedback unserer Mitarbeiter war durchwegs positiv.“ — greiner packaging (Logo falls vorhanden)
- **Einbettung:** Zitate in Anführungszeichen, zentriert, Montserrat Light; Autorenname darunter, kleiner
- **Wavy Divider am Ende** (Blue→White) – direkt mit einbauen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 8: Kontakt

### Schritt 20: Kontakt-Sektion
- **Überschrift (Script):** „KONTAKT“
- **Subline (Montserrat Light):** „HAST DU FRAGEN ODER EINE BUCHUNG ODER ANLIEGEN?“ oder „WIE ERREICHST DU MICH AM SCHNELLSTEN?“ oder „ICH FREUE MICH AUF DEINE NACHRICHT“
- **Intro-Text (Montserrat Light):** „Gerne beantworte ich deine persönlichen Anfragen für maßgeschneiderte Kurse oder vielseitige Impulse (Workshops).“
- **Kontaktdaten:** Telefon `Icons/Telefon White.png` + `+43 699 191 121 75` | E-Mail `Icons/Mail White.png` + `corina.ung@gmx.at`
- **Social:** Instagram, WhatsApp (`Icons/Instagram White.png`, `Icons/WhatsApp White.png`) | Text: „Bleibe immer auf dem Laufenden“ / „Folge mir auf Social Media für aktuelle News, Stundenpläne und Angebote.“
- **Button:** `Button_White_Images_Ina.png` (oder passende Variante) | Text: jetzt Whatsapp | Schrift: Montserrat Light, uppercase | Ziel: `https://chat.whatsapp.com/DwraiBvZvRE1MqUzv0vqql`
- **Adresse:** Corina Ung, Kerblweg 45, 4663 Laakirchen
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 21: Karte
- Google Maps: `https://maps.app.goo.gl/spmmWdakpuX3nmtu5` und `https://maps.app.goo.gl/RtTA4rUdqrWntqek8`
- Karte eingebettet oder verlinkt
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

---

## Phase 9: Abschluss

### Schritt 22: Referenz – Breakpoints & responsive Verhalten
- **Nur zur Orientierung** (bereits in Schritt 3c definiert, bei jeder Sektion angewendet):
- Mobile: 320px+ | Tablet: 768px | Desktop: 1024px+
- Hero: Mobile-Bild vs. Desktop-Bild (picture/srcset)
- Events: Liste (Mobile) vs. Slideshow (Desktop)
- Header: Burger (Mobile) vs. horizontale Nav (Desktop)

### Schritt 23: Unterseiten
- **Über mich:** `images/HeroÜberMich_Image_Ina_Mobile.png` / `_Desktop.png` | `images/MeinStil_Image_Ina.png` | `images/SoftFacts_Image_Ina.png`
  - **Texte (aus Webdesign):** „Zwischen was... und wo...“ (Script) | Fließtext zu Philosophie, Meditationen, Entspannungsrituale, individuell zugeschnitten | „Soft Facts“ + „Auf einen Blick“: Expertise (Hatha, Restorative Yoga), Körperarbeit, Stärken, Standorte | „Bereit für deine Auszeit?“ „Gleich anmelden und dich wohlfühlen.“
- **Firmenyoga:** `images/HeroFirmenyoga_Image_Ina_Mobile.png` / `_Desktop.png` | `images/Benefits_Image_Ina.png`
  - **Texte (aus Webdesign):** „Bewegung, die verbindet. Ruhe, die wirkt.“ | „Für mehr Fokus und Energie im Team: maßgeschneiderte Yoga-Einheiten – vor Ort oder online.“ | „Ausgleich“ / „FÜR KÖRPER & GEIST“ | Benefits: Gesundheit, Fokus, Teamgeist, Ausgleich (jeweils mit Kurzbeschreibung)
- **Blog:** Platzhalter-Struktur
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 24: Footer
- Impressum, Datenschutz, Kontakt
- Hintergrund: Off-white `#f7f8f6`
- **Mobile-first + responsive (Breakpoints) – sofort mit einbauen**

### Schritt 25: Finale Checks
- Alle Bilder aus `images/` genutzt
- Farben aus Briefing
- Schriften aus Briefing
- Anker-Links funktionsfähig
- Kontakt-Links (WhatsApp, Mail, Instagram) korrekt

---

## Übersicht: Ziele der Website

| Ziel | Umsetzung |
|------|-----------|
| Events sichtbar | Events-Sektion mit Slideshow/Liste + Pop-up-Details |
| Infos zu Yoga-Angeboten | Angebote, Yoga, Sound Bath, Nuad Thai mit Beschreibungen |
| Kontakt | Kontakt-Sektion + Header-Icons + Buttons „KONTAKTIERE MICH“ |

---

## Hinweise

### Mobile-First + Responsive von Anfang an
- Jede Sektion wird beim Bauen **direkt** für alle Breakpoints umgesetzt (siehe Schritt 3c)
- **Fertig = fertig:** Wenn eine Sektion fertig ist, muss sie nicht mehr angefasst werden – keine Nacharbeit

### Stil-Konsistenz (User muss sich auskennen)
- **Einheitliche Typografie:** Script für Überschriften, Montserrat Light für alles Weitere – über gesamte Website
- **Klare Sektionen:** Jede Sektion hat erkennbare Struktur (Überschrift → Text → Button/CTA)
- **Wiederkehrende Elemente:** Wavy Dividers, Button-Styles, Farbpalette aus Briefing – User erkennt Muster
- **Orientierung:** Kontakt, Events, Kurse immer über gleiche Anker/Navigation erreichbar

### Wavy Dividers
Die Wavy Dividers werden **nicht** in einer separaten Phase eingefügt, sondern **direkt mit jeder Sektion** von oben nach unten gebaut. So ist jede Sektion inklusive Übergang fertig, bevor die nächste kommt.

### Buttons
Die Buttons sind **in jeder Sektion** mit passender Farbe/Asset, Text und Ziel spezifiziert. **Schrift für alle Buttons einheitlich:** Montserrat Light, uppercase (aus Briefing).

### Texte aus Webdesign
Alle Texte sind **in der passenden Sektion** mit Einbettung (Headline, Fließtext, Subline) vermerkt. Keine Texte „irgendwo“ – User findet konsistente Sprache und Struktur.

