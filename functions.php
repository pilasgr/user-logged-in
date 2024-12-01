////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ειδοποίηση σύνδεσης χρήστη με email
function send_login_notification($user_login, $user) {

    // Μην αποστείλεις ειδοποίηση αν το username είναι "pilasgr"
    if ($user_login === 'pilasgr') { // Εδώ ορίζετε το δικό σας username για να ΜΗΝ λαμβάνετε ειδοποίηση για τη δική σας σύνδεση
        return;
    }

    // Εξαίρεση ρόλων
    $excluded_roles = array(
        'subscriber',         // Εξαίρεση συνδρομητή
        'customer',         // Εξαίρεση πελάτη
    );

    // Έλεγχος αν ο ρόλος του χρήστη δεν είναι στην εξαίρεση
    if (!array_intersect($excluded_roles, (array) $user->roles)) {

        // Πάρε το email αποστολέα από τις ρυθμίσεις του site
        $to = 'info@pilas.gr'; // Εδώ βάζετε το δικό σας e-mail ώστε να έρχεται σε εσάς η ειδοποίηση. Αντίστοιχα μπορείτε να ορίσετε όποιον παραλήπτη θέλετε.
        $sender_name = get_bloginfo('name');
        $sender_email = get_option('admin_email');

        // Πάρτε το πλήρες όνομα του χρήστη
        $user_fullname = trim($user->first_name . ' ' . $user->last_name);

        // Ορίστε τη ζώνη ώρας στην τοποθεσία Αθήνα
        date_default_timezone_set('Europe/Athens');

        // Πληροφορίες που θα σταλούν
        $subject = "($sender_name) Ο χρήστης $user_fullname μόλις συνδέθηκε";
        $date = date('d-m-Y');
        $time = date('H:i');
        $device = $_SERVER['HTTP_USER_AGENT'];

        // Minimal HTML περιεχόμενο email
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                <h2 style='text-align: center; color: #e82064;'>Ειδοποίηση Σύνδεσης Χρήστη</h2>
                <p>Ο παρακάτω χρήστης μόλις συνδέθηκε στον λογαριασμό του:</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Χρήστης:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>$user_login</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Πλήρες Όνομα:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>$user_fullname</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Ημερομηνία:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>$date</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Ώρα:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>$time</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 8px;'><strong>Συσκευή:</strong></td>
                        <td style='border: 1px solid #ddd; padding: 8px;'>$device</td>
                    </tr>
                </table>
                <p style='text-align: center; font-size: 0.9em; color: #e82064;'>Αυτό είναι ένα αυτοματοποιημένο μήνυμα. Παρακαλούμε μην απαντήσετε σε αυτό το email.</p>
                <p style='text-align: center; font-size: 0.9em; color: #555; margin-top: 20px;'>Powered with ❤ by <a href='https://www.pilas.gr' style='color: #e82064; text-decoration: none;'>Pilas.Gr</a></p>
            </div>
        </body>
        </html>
        ";

        // Headers
        $headers = array(
            'From: ' . $sender_name . ' <' . $sender_email . '>',
            'Content-Type: text/html; charset=UTF-8'
        );

        // Αποστολή email
        if (!wp_mail($to, $subject, $message, $headers)) {
            error_log('Αποτυχία αποστολής email σύνδεσης.');
        }
    }
}

add_action('wp_login', 'send_login_notification', 10, 2);

////////////////////////////////////////////////////////////////////////////////////////////////////////////
