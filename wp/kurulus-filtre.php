<?php
/**
 * Plugin Name: Kuruluş Filtreli Tablosu (Admin Menü + Kısa Kod)
 * Description: Admin menüsü ve düzenleme paneli olan filtreli kuruluş tablosu. [kurulus_tablosu] ile sayfada göster.
 * Version: 1.2
 * Author: Sen
 */

// Veritabanı tablosu oluştur (indirim ve anlaşma eklenmiş)
register_activation_hook(__FILE__, 'kft_tablosu_olustur');
function kft_tablosu_olustur() {
    global $wpdb;
    $tablo = $wpdb->prefix . 'kuruluslar';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tablo (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ad VARCHAR(255) NOT NULL,
        kategori VARCHAR(100) NOT NULL,
        sehir VARCHAR(100) NOT NULL,
        indirim VARCHAR(255) DEFAULT NULL,
        anlasma_detaylari TEXT DEFAULT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Admin menü
add_action('admin_menu', 'kft_admin_menusu');
function kft_admin_menusu() {
    add_menu_page('Kuruluşlar', 'Kuruluşlar', 'manage_options', 'kuruluslar', 'kft_admin_sayfa');
}

// Admin sayfa içeriği (indirim ve anlaşma detayları eklenmiş)
function kft_admin_sayfa() {
    global $wpdb;
    $tablo = $wpdb->prefix . 'kuruluslar';

    // Ekle
    if (isset($_POST['ekle'])) {
        $wpdb->insert($tablo, [
            'ad' => sanitize_text_field($_POST['ad']),
            'kategori' => sanitize_text_field($_POST['kategori']),
            'sehir' => sanitize_text_field($_POST['sehir']),
            'indirim' => sanitize_text_field($_POST['indirim']),
            'anlasma_detaylari' => sanitize_textarea_field($_POST['anlasma_detaylari']),
        ]);
        echo '<div class="updated"><p>Kuruluş eklendi.</p></div>';
    }

    // Sil
    if (isset($_GET['sil'])) {
        $wpdb->delete($tablo, ['id' => intval($_GET['sil'])]);
        echo '<div class="updated"><p>Kuruluş silindi.</p></div>';
    }

    // Güncelle
    if (isset($_POST['guncelle'])) {
        $wpdb->update($tablo, [
            'ad' => sanitize_text_field($_POST['ad']),
            'kategori' => sanitize_text_field($_POST['kategori']),
            'sehir' => sanitize_text_field($_POST['sehir']),
            'indirim' => sanitize_text_field($_POST['indirim']),
            'anlasma_detaylari' => sanitize_textarea_field($_POST['anlasma_detaylari']),
        ], ['id' => intval($_POST['id'])]);
        echo '<div class="updated"><p>Kuruluş güncellendi.</p></div>';
    }

    // Güncellenecek veriyi al
    $duzenle = null;
    if (isset($_GET['duzenle'])) {
        $duzenle = $wpdb->get_row("SELECT * FROM $tablo WHERE id=" . intval($_GET['duzenle']));
    }

    // Form
    ?>
    <div class="wrap">
        <h2>Kuruluş Ekle / Düzenle</h2>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $duzenle->id ?? ''; ?>">
            <p><input type="text" name="ad" placeholder="Ad" required value="<?php echo $duzenle->ad ?? ''; ?>"></p>
            <p><input type="text" name="kategori" placeholder="Kategori" required value="<?php echo $duzenle->kategori ?? ''; ?>"></p>
            <p><input type="text" name="sehir" placeholder="Şehir" required value="<?php echo $duzenle->sehir ?? ''; ?>"></p>
            <p><input type="text" name="indirim" placeholder="İndirim" value="<?php echo $duzenle->indirim ?? ''; ?>"></p>
            <p><textarea name="anlasma_detaylari" placeholder="Anlaşma Detayları"><?php echo $duzenle->anlasma_detaylari ?? ''; ?></textarea></p>
            <p>
                <?php if ($duzenle): ?>
                    <button type="submit" name="guncelle" class="button-primary">Güncelle</button>
                <?php else: ?>
                    <button type="submit" name="ekle" class="button-primary">Ekle</button>
                <?php endif; ?>
            </p>
        </form>

        <h2>Kuruluşlar</h2>
        <table class="widefat striped">
            <thead>
                <tr><th>ID</th><th>Ad</th><th>Kategori</th><th>Şehir</th><th>İndirim</th><th>Detay</th><th>İşlem</th></tr>
            </thead>
            <tbody>
            <?php
            $veriler = $wpdb->get_results("SELECT * FROM $tablo ORDER BY id DESC");
            foreach ($veriler as $v) {
                echo "<tr>
                    <td>$v->id</td>
                    <td>$v->ad</td>
                    <td>$v->kategori</td>
                    <td>$v->sehir</td>
                    <td>$v->indirim</td>
                    <td><button onclick=\"detayGoster('$v->anlasma_detaylari')\">Detay</button></td>
                    <td>
                        <a href='?page=kuruluslar&duzenle=$v->id'>Düzenle</a> |
                        <a href='?page=kuruluslar&sil=$v->id' onclick=\"return confirm('Silmek istediğine emin misin?');\">Sil</a>
                    </td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <script>
    function detayGoster(detay) {
        alert(detay);
    }
    </script>
    <?php
}

// Kısa kod: Sayfa içinde filtreli gösterim (indirim ve anlaşma detayları eklenmiş)
add_shortcode('kurulus_tablosu', 'kurulus_tablosu_shortcode');
function kurulus_tablosu_shortcode() {
    global $wpdb;
    $tablo = $wpdb->prefix . 'kuruluslar';
    $veriler = $wpdb->get_results("SELECT * FROM $tablo");

    ob_start();
    ?>
    <div class="kurulus-filtre">
        <style>
            .kurulus-filtre select {margin-right:10px;padding:5px;}
            .kurulus-filtre table {width:100%;border-collapse:collapse;margin-top:10px;}
            .kurulus-filtre th, .kurulus-filtre td {border:1px solid #ccc;padding:8px;}
        </style>
        <label>Kategori:
            <select id="kategori">
                <option value="">Tümü</option>
                <?php foreach (array_unique(array_map(fn($v) => $v->kategori, $veriler)) as $k): ?>
                    <option><?php echo esc_html($k); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Şehir:
            <select id="sehir">
                <option value="">Tümü</option>
                <?php foreach (array_unique(array_map(fn($v) => $v->sehir, $veriler)) as $s): ?>
                    <option><?php echo esc_html($s); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <table id="tablo">
            <thead><tr><th>Şehir</th><th>Kategori</th><th>Kurum & Kuruluş</th><th>İndirim (%)</th><th>Detay</th></tr></thead>
            <tbody></tbody>
        </table>

        <div id="detay" style="display:none;border:1px solid #ccc;padding:10px;margin-top:10px;">
            <h3>Detay</h3>
            <div id="detayIcerik"></div>
            <button onclick="geriDon()">Geri Git</button>
        </div>

        <script>
        const veriler = <?php echo json_encode($veriler); ?>;

        function tabloyuGoster() {
            const kategori = document.getElementById('kategori').value;
            const sehir = document.getElementById('sehir').value;
            const tbody = document.querySelector('#tablo tbody');
            tbody.innerHTML = '';

            veriler
                .filter(v => (!kategori || v.kategori === kategori) && (!sehir || v.sehir === sehir))
                .forEach((v, i) => {
                    tbody.innerHTML += `<tr>
                        
                        <td>${v.sehir}</td>
                        <td>${v.kategori}</td>
                        <td>${v.ad}</td>
                        <td>${v.indirim}</td>
                        <td><button onclick="detayGoster(${i})">Detay</button></td>
                    </tr>`;
                });
            document.getElementById('detay').style.display = 'none';
        }

        function detayGoster(i) {
            const v = veriler[i];
            document.getElementById('detayIcerik').innerHTML = `
                <p><strong>Ad:</strong> ${v.ad}</p>
                <p><strong>Kategori:</strong> ${v.kategori}</p>
                <p><strong>Şehir:</strong> ${v.sehir}</p>
                <p><strong>İndirim:</strong> ${v.indirim}</p>
                <p><strong>Anlaşma Detayları:</strong> ${v.anlasma_detaylari}</p>
            `;
            document.getElementById('detay').style.display = 'block';
        }

        function geriDon() {
            document.getElementById('detay').style.display = 'none';
        }

        document.getElementById('kategori').addEventListener('change', tabloyuGoster);
        document.getElementById('sehir').addEventListener('change', tabloyuGoster);

        tabloyuGoster();
        </script>
    </div>
    <?php
    return ob_get_clean();
}

// === REST API DESTEĞİ ===
add_action('rest_api_init', function () {
    register_rest_route('kurulus/v1', '/list', [
            'methods' => 'GET',
            'callback' => 'kft_rest_listesi',
            'permission_callback' => '__return_true', // herkese açık (isteğe göre sınırlandırılabilir)
    ]);
});

function kft_rest_listesi($request) {
    global $wpdb;
    $tablo = $wpdb->prefix . 'kuruluslar';

    $kategori = sanitize_text_field($request->get_param('kategori'));
    $sehir = sanitize_text_field($request->get_param('sehir'));

    $kosullar = [];
    if ($kategori) $kosullar[] = $wpdb->prepare("kategori = %s", $kategori);
    if ($sehir) $kosullar[] = $wpdb->prepare("sehir = %s", $sehir);

    $where = $kosullar ? "WHERE " . implode(" AND ", $kosullar) : "";
    $veriler = $wpdb->get_results("SELECT * FROM $tablo $where ORDER BY id DESC");

    return rest_ensure_response($veriler);
}
?>
