<?php
    include 'session_cookie_check.php';
    $pageTitle = 'About us';
    include 'header.php';

?>
<!-- Main Content Section -->
<div class="content-container">
        <h2>1. Introducere</h2>
        <p>Aplicația destinata unei sali de antrenament permite gestionarea completă a programelor de antrenament, la care clientii se pot conecta pentru a lucra cu antrenorii.</p>

        <h2>2. Tehnologii Utilizate</h2>
        <ul>
            <li><strong>Backend:</strong> PHP pentru procesarea logicii aplicației.</li>
            <li><strong>Bază de Date:</strong> MySQL pentru stocarea și gestionarea datelor despre utilizatori, programe și sesiuni.</li>
            <li><strong>Frontend:</strong> HTML, CSS, JavaScript pentru un design responsive.</li>
            <li><strong>Sisteme de notificare:</strong> PHPMailer pentru trimiterea notificărilor de email la inregistrarea in aplicatie.</li>
            <li><strong>Realizare de rapoarte:</strong> FPDF pentru exportul unui raport legat de testul Cooper.</li>
        </ul>

        <h2>3. Roluri în Aplicație</h2>
        <h3>1. Admin:</h3>
        <ul>
            <li>Gestionare utilizatori: șterge utilizatori.</li>
            <li>Aprobarea cererilor antrenorilor: Admin decide dacă un antrenor poate preda un anumit program.</li>
        </ul>
        
        <h3>2. Antrenor:</h3>
        <ul>
            <li>Preda cursuri: Antrenorul se poate înscrie la cursuri pentru a le preda, cu acceptul adminului.</li>
        </ul>

        <h3>3. Client:</h3>
        <ul>
            <li>Vizualizarea programelor disponibile: Clienții pot vizualiza programele de antrenament disponibile.</li>
            <li>Înscrierea la programe: Clienții se pot înscrie la programele dorite.</li>
            <li>Efectuarea Testului Cooper: Clientii pot face testul Cooper pentru a-si determina nivelul de VO2Max, iar apoi sa descarce un raport cu datele acelea.</li>
        </ul>

        <h2>4. Structura Bazei de Date</h2>
        <p>Baza de date pentru aplicația ta se poate împărți în mai multe tabele care reflectă structura aplicației tale.</p>

        <h3>1. Tabel: users</h3>
        <p>Informațiile despre utilizatori (antrenori, clienți și administratori).</p>

        <h3>2. Tabel: training_programs</h3>
        <p>Informațiile despre programele de antrenament disponibile (tip, durata, pret).</p>

        <h3>3. Tabel: clients_programs</h3>
        <p>Înscrierile clienților la programele de antrenament.</p>

        <h3>4. Tabel: contact_form_submissions</h3>
        <p>Datele trimise de utilizatori prin formularul de contact.</p>

        <h3>5. Tabel: country_phone_codes</h3>
        <p>Lista de prefixe de telefon necesare la inregistrarea in aplicatie.</p>

        <h3>6. Tabel: login_cookie_tokens</h3>
        <p>Inregistrarea cookie-urilor pentru Remember me.</p>

        <h3>7. Tabel: trainer_course_requests</h3>
        <p>Lista de cereri de predare a antrenamentelor.</p>

        <h2>5. Fluxuri Principale</h2>
        <h3>Înscrierea unui client la un program:</h3>
        <ul>
            <li>Clienții pot vizualiza programele de antrenament disponibile.</li>
            <li>Dacă un program este disponibil, clientul se poate înscrie.</li>
        </ul>

        <h3>Înscrierea unui antrenor la un program:</h3>
        <ul>
            <li>Antrenorii pot vizualiza programele de antrenament disponibile.</li>
            <li>Dacă un program este disponibil, antrenorul se poate înscrie.</li>
        </ul>

        <h3>Admin-ul poate vizualiza utilizatorii și poate aproba cererile antrenorilor de a ține programe de antrenament.</h3>

    </div> <!-- End Content -->

</body>
</html>