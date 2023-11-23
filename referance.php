<?php
/*
Plugin Name: Referans Firma Bilgileri
Description: Özel bir WordPress eklentisi - Referans firma bilgileri yönetimi.
Version: 1.0
Author: Ömer Süt
*/

register_activation_hook(__FILE__, "my_custom_plugin_activate");

function my_custom_plugin_activate()
{
    global $wpdb;

    $table_name = $wpdb->prefix . "referance_firma_bilgileri";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        firma_adi varchar(255) NOT NULL,
        logo_url varchar(255) NOT NULL,
        ulkeler text,
        referanslar text,
        firma_url text,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . "wp-admin/includes/upgrade.php";
    dbDelta($sql);


}

add_action("admin_menu", "my_custom_plugin_admin_menu");

function my_custom_plugin_admin_menu()
{
    add_menu_page(
        "Referans Firma Bilgileri",
        "Firma Bilgileri",
        "manage_options",
        "my_custom_plugin",
        "my_custom_plugin_page"
    );
    add_submenu_page(
        "my_custom_plugin",
        "Veri Listesi",
        "Veri Listesi",
        "manage_options",
        "my_custom_plugin_list",
        "my_custom_plugin_list_page"
    );
}

function my_custom_plugin_page()
{
    global $wpdb;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_id"])) {
        $edit_id = absint($_POST["edit_id"]);
        $firma_adi = sanitize_text_field($_POST["firma_adi"]);
        $logo_url = esc_url_raw($_POST["logo_url"]);
        $ulkeler = sanitize_text_field($_POST["ulkeler"]);
        $referanslar = sanitize_text_field($_POST["referanslar"]);
        $firma_url = esc_url_raw($_POST["firma_url"]);

        $table_name = $wpdb->prefix . "referance_firma_bilgileri";

        $wpdb->update(
            $table_name,
            [
                "firma_adi" => $firma_adi,
                "logo_url" => $logo_url,
                "ulkeler" => $ulkeler,
                "referanslar" => $referanslar,
                "firma_url" => $firma_url,
            ],
            ["id" => $edit_id]
        );
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firma_adi = sanitize_text_field($_POST["firma_adi"]);
        $logo_url = esc_url_raw($_POST["logo_url"]);
        $ulkeler = sanitize_text_field($_POST["ulkeler"]);
        $referanslar = sanitize_text_field($_POST["referanslar"]);
        $firma_url = esc_url_raw($_POST["firma_url"]);

        $table_name = $wpdb->prefix . "referance_firma_bilgileri";

        $wpdb->insert($table_name, [
            "firma_adi" => $firma_adi,
            "logo_url" => $logo_url,
            "ulkeler" => $ulkeler,
            "referanslar" => $referanslar,
            "firma_url" => $firma_url,
        ]);
    }
    ?>
    <div class="wrap">
        <h2>Referans Firma Bilgileri</h2>
        <form method="post" action="">
            <label for="firma_adi">Firma Adı:</label>
            <input type="text" name="firma_adi" required>
            <br>
            <label for="logo_url">Logo URL:</label>
            <input type="text" name="logo_url" required>
            <br>
            <label for="ulkeler">Ülkeler:</label>
            <textarea name="ulkeler"></textarea>
            <br>
            <label for="referanslar">Referanslar:</label>
            <textarea name="referanslar"></textarea>
            <br>
            <label for="firma_url">Firma URL:</label>
            <input type="text" name="firma_url" required>
            <br>
            <input type="submit" class="button-primary" value="Veri Ekle">
        </form>
    </div>
    <?php
}

function my_custom_plugin_list_page()
{
    global $wpdb;

    if (
        isset($_GET["action"]) &&
        $_GET["action"] == "edit" &&
        isset($_GET["id"])
    ) {
        $id = absint($_GET["id"]);
        $table_name = $wpdb->prefix . "referance_firma_bilgileri";
        $result = $wpdb->get_row(
            "SELECT * FROM $table_name WHERE id = $id",
            ARRAY_A
        );

        if ($result) { ?>
            <div class="wrap">
                <h2>Referans Firma Bilgileri - Düzenleme</h2>
                <form method="post" action="">
                    <input type="hidden" name="edit_id" value="<?php echo $result[
                        "id"
                    ]; ?>">
                    <label for="firma_adi">Firma Adı:</label>
                    <input type="text" name="firma_adi" value="<?php echo esc_attr(
                        $result["firma_adi"]
                    ); ?>" required>
                    <br>
                    <label for="logo_url">Logo URL:</label>
                    <input type="text" name="logo_url" value="<?php echo esc_url(
                        $result["logo_url"]
                    ); ?>" required>
                    <br>
                    <label for="ulkeler">Ülkeler:</label>
                    <textarea name="ulkeler"><?php echo esc_textarea(
                        $result["ulkeler"]
                    ); ?></textarea>
                    <br>
                    <label for="referanslar">Referanslar:</label>
                    <textarea name="referanslar"><?php echo esc_textarea(
                        $result["referanslar"]
                    ); ?></textarea>
                    <br>
                    <label for="firma_url">Firma URL:</label>
                    <input type="text" name="firma_url" value="<?php echo esc_url(
                        $result["firma_url"]
                    ); ?>" required>
                    <br>
                    <input type="submit" class="button-primary" value="Veriyi Güncelle">
                </form>
            </div>
            <?php }
    } else {
        if (
            isset($_GET["action"]) &&
            $_GET["action"] == "delete" &&
            isset($_GET["id"])
        ) {
            $id = absint($_GET["id"]);
            $table_name = $wpdb->prefix . "referance_firma_bilgileri";
            $wpdb->delete($table_name, ["id" => $id]);
        } ?>
        <div class="wrap">
            <h2>Referans Firma Bilgileri - Veri Listesi</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Firma Adı</th>
                        <th>Logo URL</th>
                        <th>Ülkeler</th>
                        <th>Referanslar</th>
                        <th>Firma URL</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $table_name = $wpdb->prefix . "referance_firma_bilgileri";
                    $results = $wpdb->get_results(
                        "SELECT * FROM $table_name",
                        ARRAY_A
                    );

                    foreach ($results as $row) { ?>
                        <tr>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo $row["firma_adi"]; ?></td>
                            <td><?php echo $row["logo_url"]; ?></td>
                            <td><?php echo $row["ulkeler"]; ?></td>
                            <td><?php echo $row["referanslar"]; ?></td>
                            <td><?php echo $row["firma_url"]; ?></td>
                            <td>
                                <a href="?page=my_custom_plugin_list&action=edit&id=<?php echo $row[
                                    "id"
                                ]; ?>">Düzenle</a>
                                |
                                <a href="?page=my_custom_plugin_list&action=delete&id=<?php echo $row[
                                    "id"
                                ]; ?>" onclick="return confirm('Bu veriyi silmek istediğinizden emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

// Shortcode ile verileri çağırma
add_shortcode("referance-card", "my_custom_plugin_shortcode");

function my_custom_plugin_shortcode($atts)
{
    global $wpdb;

    // Shortcode parametrelerini al
    $atts = shortcode_atts(
        [
            "referans" => "",
            "ulke" => "",
        ],
        $atts,
        "referance-card"
    );

    // Filtreleme için SQL sorgusu oluştur
    $where_clause = "";

    if (!empty($atts["referans"])) {
        $where_clause .= " AND FIND_IN_SET('{$atts["referans"]}', referanslar) > 0";
    }

    if (!empty($atts["ulke"])) {
        $where_clause .= " AND FIND_IN_SET('{$atts["ulke"]}', ulkeler) > 0";
    }

    ob_start();
    ?>
 <div class="referance-cards">
    <?php
    $table_name = $wpdb->prefix . "referance_firma_bilgileri";
    $results = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY firma_adi",
        ARRAY_A
    );

    foreach ($results as $row) { ?>
<style>
    .referance-cards {
     display: flex;
     flex-wrap: wrap;
     justify-content: space-around;
}
 .referance-card {
     filter: grayscale(100%);
     -webkit-filter: grayscale(100%);
     -moz-filter: grayscale(100%);
     flex: 0 0 calc(33.33% - 20px);
     margin: 10px;
     box-sizing: border-box;
}
 @media (max-width: 767px) {
     .referance-card {
         flex: 0 0 calc(100% - 20px);
    }
}
 .referance-card {
     transition: filter 1s;
     border: 1px solid #ccc;
     border-radius: 3px;
     overflow: hidden;
     margin: 10px;
     text-align: center;
     position: relative;
}
 .referance-card:hover{
     filter: grayscale(0%);
     -webkit-filter: grayscale(0%);
     -moz-filter: grayscale(0%);
}
 .referance-card:hover .badge {
     opacity: 1;
}
 .logo{
     height: 60px !important;
     margin-top: 10px !important;
}
 .country{
     height: 13px !important;
     border-radius: 2px !important;
}
 .badge{
     transition: opacity 1s;
     opacity: 0;
     text-decoration: none !important;
     background: #e45a86;
     padding: 4px 8px;
     color: #ffffff !important;
     font-weight: 600;
     margin: 2.5px;
     box-sizing: border-box;
}
.title{
    padding: 10px;
}
.badge-area{
    display: flex;
     flex-wrap: wrap;
     justify-content: center;
}
.website {
    width: 20px;
    position: absolute;
    right: 10px;
    top: 10px;
}

</style>
        <div class="referance-card">
            <div class="card-body">
            <?php if (!empty($row["firma_url"])) { ?>
                <a href="<?php echo $row[
                    "firma_url"
                ]; ?>" class="website"><img src="https://cdn-icons-png.flaticon.com/512/3214/3214746.png"></a>
                <?php } ?>

                <img class="logo" src="<?php echo $row[
                    "logo_url"
                ]; ?>" alt="<?php echo $row["firma_adi"]; ?> Logo">
                <h3 class="title"><?php echo $row["firma_adi"]; ?></h3>
                <p class="badge-area"> <?php echo display_badges_referance(
                    $row["referanslar"]
                ); ?></p>
                <p> <?php echo display_badges_flags($row["ulkeler"]); ?></p>
            </div>
        </div>
        <?php }
    ?>
</div>
    <?php return ob_get_clean();
}

function display_badges_flags($data)
{
    $items = explode(",", $data);

    $badges = array_map(function ($item) {
        return '<img src="' . flags($item) . '" class="country"/>';
    }, $items);

    return implode(" ", $badges);
}

function flags($item)
{
    switch ($item) {
        case "tr":
            return "https://cdn.countryflags.com/thumbs/turkey/flag-800.png"; // custom flags
        default:
            return "";
    }
}

function display_badges_referance($data)
{
    $items = explode(",", $data);

    $badges = array_map(function ($item) {
        return '<a href="' .
            referanceUrl($item) .
            '" class="badge">' .
            trim($item) .
            "</a>";
    }, $items);

    return implode(" ", $badges);
}

function referanceUrl($item)
{
    switch ($item) {
        case "referance":
            return "referance.com"; // custom referance urls
        default:
            return "";
    }
}

?>
