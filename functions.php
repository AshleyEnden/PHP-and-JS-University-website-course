<?php 

function pageBanner($args = NULL) { //=NULL zet je neer zodat het ook een optie is om geen args/argumenten op te geven bij het aanroepen van de functie. 
    
    if (!isset($args['title'])) { //defaultwaarde instellen voor als bij het oproepen van de functie pageBanner op een pagina niet specifiek een waarde in de array voor title wordt opgegeven, dan pakt ie de titel van de pagina door onderstaande formule. Vandaar dat er ! voor het argument staat, dat betekent dat het niet leeg is. 
    //Latere toevoeging is de !isset geweest, waardoor de ! voor $args weg kon. !isset is toegevoegd omdat je in nieuwere PHP niet in een array-item kan kijken die niet bestaat, dan krijg je een waarschuwingsbericht op je website te zien. Deze !isset tool maakt het wel mogelijk, op een een of andere veilige manier.
        $args['title'] = get_the_title();
    }

    if (!isset($args['subtitle'])) { 
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!isset($args['photo'])) { //defaultwaarde voor background image instellen. checkt eerst of er in de post- of pagina.php een waarde(url) voor 'photo'-array is opgegeven. Zo niet, kijkt ie of er voor de pagina of post een afbeelding is geupload via het ACF field page_banner_background_image. Als dat ook niet zo is, zal de images/ocean.jpg worden weergegeven.
    //Latere toevoegen aan onderstaande code is de 'AND !is_archive() AND !is_home()' geweest, wat voorkomt dat eventuele onduidelijkheden in welke bannenphoto de website moet pakken wordt voorkomen.
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() )  { 
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else { 
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }


    ?> 
    <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div> 
      <div class="page-banner__content container container--narrow">
        <!-- ter info: als je in een functie zoals $pageBannerImage van hierboven wilt kijken welke waarden, bijvoorbeeld een array, hierin zitten, kan je de volgende code <?php //print_r($pageBannerImage); ?> uitvoeren om tijdelijk op de webpagina te krijgen wat er in de functie verschuild. Zodat je erachter kan komen dat je via array de 'url' dient te hebben. Daarna is de 'url' in bovenstaande code aangepast om een image van aangepaste 'sizes' op te halen, die staat ook in de tekst die het output! 
        Via de plugin Plugin Manual Image Crop kan je in Wordpress Admin binnen de post waarin je de banner hebt toegevoegd in het ACF-veld de specifieke foto aanpassen en de crop aanpassen. -->
        <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
        <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
        </div>
      </div>
    </div>
<?php }

function university_files() { //hieronder kan je javascripts ophalen, zoals die van Google om te gebruiken voor Google Maps.
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIza...', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}  

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
    //register_nav_menu('headerMenuLocation', 'Header Menu Location'); //Hiermee activeer je in Wordpress admin de optie Menus onder Appearance -> Menus. Onderstaande 2 opties worden ook toegevoegd aan Menu Structure -> Menu Settings -> Discplay Location - om aan te vinken waar de menu's toe behoren. In header.php en footer.php geef je de locatie op van de menu's.
    //register_nav_menu('footerLocationOne', 'Footer Location One'); 
    //register_nav_menu('footerLocationTwo', 'Footer Location Two');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails'); //featured images toevoegen in thema als optie voor posts - in cursusgeval gebruikt bij professors. Hierbij dien je ook wat toe te voegen in de mu-plugin bestand (Supports => array('thumbnail')).
    add_image_size('professorLandscape', 400, 260, true); //als je een afbeelding upload in Wordpress maakt Wordpress automatisch kopieën hiervan in kleinere formaten. Deze functie zorgt ervoor dat Wordpress nog een afbeelding toevoegt die voldoet aan de hierboven opgegeven pixel-maten. Naam formaat, pixels breed, pixels hoog, true=crop de afbeelding naar de opgegeven breedte én hoogte. false=defaultsetting en betekent dat afbeelding naar tenminste 1 van de breedte- en hoogtepixelmaten wordt aangepast; afhankelijk van andere dingen op de webpagina. 
    //Je kan true of false ook vervangen met een array(horizontaal left, right + verticaal bottom, up). Bijvoorbeeld array('left', 'top'). Dit gebeurt dan wel op elke image. Als je dit per image aan wilt passen heb je een nieuwe Plugin nodig: Manual Image Crop by Tomasz Sita.
    //Dit wordt nu enkel toegepast op nieuw toegevoegd media in Wordpress. Je hebt de Plugin Regenerate Thumbnails van Alex Mills nodig om dit toe te passen op oudere foto's. Dit doe je dan in Wordpress admin onder Tools. Binnen Wordpress Admin ga je naar professor->featured image->Crop image staat in hyperlink. Hier kan je voor elk formaat (zie tabbladen) het cropgebied aanpassen.
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features'); //moment van uitvoeren bovenstaande functions is bij after_setup_theme.

function university_adjust_queries($query) {
    if(
        !is_admin() 
        AND is_post_type_archive('program') 
        AND is_main_query()) { 
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('post_per_page', -1);        
    }

    if (
        !is_admin()/* is niet admin - zodat events in wordpress admin niet ook wordt aangepast naar onderstaande filters, je wilt enkel de weergave op je website aanpassen. */ 
        AND is_post_type_archive('event') 
        AND $query->is_main_query()) { //zodat dit niet wordt toegepast in andere weergaven waar je events op wilt halen.
            $today = date('Ymd');
            $query->set('meta_key', 'event_date'); //deze heb je al bepaald onder fron-page.php $homepageEvents.
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            $query->set('meta_query', array( //hiermee zorgen we ervoor dat events met een evenement_datum in het verleden niet worden getoond.
                array(
                  'key' => 'event_date',
                  'compare' => '>=',
                  'value' => $today,
                  'type' => 'numeric'
                )
              ));
        }
}

add_action('pre_get_posts', 'university_adjust_queries');

function universityMapKey($api) { //functie voor toevoegen van de Maps-API zoals aangemaakt in free trial account op 23-11-2024 op: https://console.cloud.google.com/. 
    $api['key'] = 'AIza...';
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');

?>