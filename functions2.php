////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ειδοποίηση σύνδεσης χρήστη με χρήση WooCommerce template

function send_login_notification($user_login, $user) {

    // Μην αποστείλεις ειδοποίηση αν το username είναι "pilasgr"
    if ($user_login === 'pilasgr') {
        return;
    }

    // Εξαίρεση ρόλων (πρόσθεσε // μπροστά για να εξαιρέσεις τον ρόλο)
    $excluded_roles = array(
        //'administrator',    // ΔΕΝ γίνεται εξαρεση διαχειριστή
        //'editor',           // ΔΕΝ γίνεται εξαρεση επιμελητή
        //'author',           // ΔΕΝ γίνεται εξαρεση συγγραφέα
        //'contributor',      // ΔΕΝ γίνεται εξαρεση συνεισφέροντα
        'subscriber',       // ΓΙΝΕΤΑΙ Εξαίρεση συνδρομητή
        //'shop_manager',     // ΔΕΝ γίνεται εξαρεση διαχειριστή καταστήματος
        'customer',         // ΓΙΝΕΤΑΙ Εξαίρεση πελάτη
    );

    // Έλεγχος αν ο ρόλος του χρήστη δεν είναι στην εξαίρεση
    if (!array_intersect($excluded_roles, (array) $user->roles)) {

        // Πάρε το email αποστολέα από τις ρυθμίσεις του site
        $to = 'info@pilas.gr';
        $sender_name = get_bloginfo('name'); // Όνομα αποστολέα από τον τίτλο του site
        $sender_email = get_option('admin_email'); // Email αποστολέα από το WordPress (πχ SMTP)

        // Πάρτε το πλήρες όνομα του χρήστη
        $user_fullname = $user->first_name . ' ' . $user->last_name;

        // Ορίστε τη ζώνη ώρας στην τοποθεσία Αθήνα
        date_default_timezone_set('Europe/Athens');

        // Πληροφορίες που θα σταλούν
        $subject = "($sender_name) Ο χρήστης $user_fullname μόλις συνδέθηκε";
        $date = date('d-m-Y');
        $time = date('H:i');
        $device = $_SERVER['HTTP_USER_AGENT'];

        // Δημιουργία περιεχομένου email
        $email_heading = 'Ειδοποίηση Σύνδεσης Χρήστη';
        $email_body = '<p>Ο παρακάτω χρήστης μόλις συνδέθηκε στον λογαριασμό του:</p>';
        $email_body .= '<table cellspacing="0" cellpadding="6" border="1" style="width: 100%; border-color: #e5e5e5;">';
        $email_body .= '<thead><tr><th style="text-align:left;">Λεπτομέρειες Σύνδεσης</th></tr></thead>';
        $email_body .= '<tbody>';
        $email_body .= '<tr><td><strong>Χρήστης:</strong> ' . $user_login . '</td></tr>';
        $email_body .= '<tr><td><strong>Πλήρες Όνομα:</strong> ' . $user_fullname . '</td></tr>';
        $email_body .= '<tr><td><strong>Ημερομηνία:</strong> ' . $date . '</td></tr>';
        $email_body .= '<tr><td><strong>Ώρα:</strong> ' . $time . '</td></tr>';
        $email_body .= '<tr><td><strong>Συσκευή:</strong> ' . $device . '</td></tr>';
        $email_body .= '</tbody>';
        $email_body .= '</table>';

        // Χρήση του προτύπου WooCommerce για την αποστολή email
        ob_start();
        wc_get_template('emails/email-header.php', array('email_heading' => $email_heading));
        echo wp_kses_post($email_body);
        wc_get_template('emails/email-footer.php');
        $message = ob_get_clean();

        // Αποστολή email
        $mailer = WC()->mailer();
        $mail_sent = $mailer->send($to, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));

        if (!$mail_sent) {
            error_log('Αποτυχία αποστολής email σύνδεσης.');
        }
    }
}

add_action('wp_login', 'send_login_notification', 10, 2);




////////////////////////////////////////////////////////////////////////////////////////////////////////////
