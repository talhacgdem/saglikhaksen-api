<?php
/*
Plugin Name: Şubeler Tablosu
Description: Şubeleri özel bir tablo üzerinde yönetir.
Version: 1.0
Author: Muhammed Talha Çiğdem
*/

if (!defined('ABSPATH')) exit;

/* === 1. TABLO OLUŞTURMA === */
register_activation_hook(__FILE__, 'subeler_create_table');
function subeler_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'subeler';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        sube_adi varchar(255) NOT NULL,
        adres text NOT NULL,
        il varchar(100) NOT NULL,
        telefon varchar(50) NOT NULL,
        baskan varchar(150) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/* === 2. ADMIN MENÜSÜ === */
add_action('admin_menu', 'subeler_admin_menu');
function subeler_admin_menu() {
    add_menu_page(
            'Şubeler',
            'Şubeler',
            'manage_options',
            'subeler',
            'subeler_admin_page',
            'dashicons-building',
            26
    );
}

/* === 3. ADMIN ARAYÜZ === */
function subeler_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'subeler';

    // Yeni ekleme işlemi
    if (isset($_POST['sube_adi'])) {
        $wpdb->insert($table_name, [
                'sube_adi' => sanitize_text_field($_POST['sube_adi']),
                'adres' => sanitize_textarea_field($_POST['adres']),
                'il' => sanitize_text_field($_POST['il']),
                'telefon' => sanitize_text_field($_POST['telefon']),
                'baskan' => sanitize_text_field($_POST['baskan']),
        ]);
        echo '<div class="updated"><p>Şube eklendi.</p></div>';
    }

    // Silme işlemi
    if (isset($_GET['delete'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['delete'])]);
        echo '<div class="updated"><p>Şube silindi.</p></div>';
    }

    $subeler = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
    ?>

    <div class="wrap">
        <h1>Şubeler</h1>

        <h2>Yeni Şube Ekle</h2>
        <form method="post">
            <table class="form-table">
                <tr><th>Şube Adı</th><td><input type="text" name="sube_adi" required style="width:100%"></td></tr>
                <tr><th>Adres</th><td><textarea name="adres" required style="width:100%"></textarea></td></tr>
                <tr><th>İl</th><td>
                        <select name="il" required style="width:100%">
                            <option value="">-- İl Seçin --</option>
                            <?php
                            $iller = [
                                    "Adana","Adıyaman","Afyonkarahisar","Ağrı","Aksaray","Amasya","Ankara","Antalya",
                                    "Ardahan","Artvin","Aydın","Balıkesir","Bartın","Batman","Bayburt","Bilecik",
                                    "Bingöl","Bitlis","Bolu","Burdur","Bursa","Çanakkale","Çankırı","Çorum",
                                    "Denizli","Diyarbakır","Düzce","Edirne","Elazığ","Erzincan","Erzurum",
                                    "Eskişehir","Gaziantep","Giresun","Gümüşhane","Hakkari","Hatay","Iğdır",
                                    "Isparta","İstanbul","İzmir","Kahramanmaraş","Karabük","Karaman","Kars",
                                    "Kastamonu","Kayseri","Kırıkkale","Kırklareli","Kırşehir","Kilis",
                                    "Kocaeli","Konya","Kütahya","Malatya","Manisa","Mardin","Mersin","Muğla",
                                    "Muş","Nevşehir","Niğde","Ordu","Osmaniye","Rize","Sakarya","Samsun",
                                    "Siirt","Sinop","Sivas","Şanlıurfa","Şırnak","Tekirdağ","Tokat","Trabzon",
                                    "Tunceli","Uşak","Van","Yalova","Yozgat","Zonguldak"
                            ];
                            foreach ($iller as $il) {
                                echo "<option value='$il'>$il</option>";
                            }
                            ?>
                        </select>
                    </td></tr>
                <tr><th>Telefon</th><td><input type="text" name="telefon" required style="width:100%"></td></tr>
                <tr><th>Şube Başkanı</th><td><input type="text" name="baskan" required style="width:100%"></td></tr>
            </table>
            <?php submit_button('Kaydet'); ?>
        </form>

        <hr>

        <h2>Mevcut Şubeler</h2>
        <table class="widefat fixed striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Şube Adı</th>
                <th>Adres</th>
                <th>İl</th>
                <th>Telefon</th>
                <th>Başkan</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($subeler as $s): ?>
                <tr>
                    <td><?php echo esc_html($s->id); ?></td>
                    <td><?php echo esc_html($s->sube_adi); ?></td>
                    <td><?php echo esc_html($s->adres); ?></td>
                    <td><?php echo esc_html($s->il); ?></td>
                    <td><?php echo esc_html($s->telefon); ?></td>
                    <td><?php echo esc_html($s->baskan); ?></td>
                    <td><a href="?page=subeler&delete=<?php echo $s->id; ?>" onclick="return confirm('Silinsin mi?')">🗑️ Sil</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* === 4. REST API === */
add_action('rest_api_init', function() {
    register_rest_route('subeler/v1', '/list', [
            'methods' => 'GET',
            'callback' => function() {
                global $wpdb;
                $table = $wpdb->prefix . 'subeler';
                $data = $wpdb->get_results("SELECT * FROM $table");
                return rest_ensure_response($data);
            }
    ]);
});
