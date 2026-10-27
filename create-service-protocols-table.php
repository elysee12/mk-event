<?php
$host = 'localhost';
$dbname = 'MK';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `service_protocols` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(150) NOT NULL,
      `photo` varchar(255) DEFAULT NULL,
      `description` text,
      `price` decimal(12,2) DEFAULT 0,
      `status` enum('available','unavailable') DEFAULT 'available',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

    $pdo->exec($sql);
    echo "Table 'service_protocols' created successfully!<br>";

    // Insert sample data
    $check = $pdo->query("SELECT COUNT(*) FROM service_protocols")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO `service_protocols` (`title`, `photo`, `description`, `price`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())");

        $samples = array(
            array('Wedding Protocol Planning', 'wedding-protocol.jpg', 'Comprehensive wedding event coordination and protocol management including ceremony flow, guest reception, and VIP handling.', 500000.00, 'available'),
            array('Corporate Event Protocol', 'corporate-protocol.jpg', 'Professional corporate event protocol services for conferences, meetings, and business gatherings with formal etiquette.', 750000.00, 'available'),
            array('State Ceremony Protocol', 'state-protocol.jpg', 'High-level protocol services for state ceremonies, diplomatic events, and official government functions.', 1200000.00, 'available')
        );

        foreach ($samples as $sample) {
            $stmt->execute($sample);
        }
        echo "Sample data inserted successfully!<br>";
    } else {
        echo "Sample data already exists.<br>";
    }

    echo "<br><a href='../pages/admin.php'>Go to Admin Panel</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
