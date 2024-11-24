<!-- Hiermee beheer je de webpagina /campus waar alle campuses(locaties) in staan -->

<?php 

get_header(); 
pageBanner(array(
  'title' => 'Our Campuses',
  'subtitle' => 'We have several conveniently located campuses.'
));
?>

<div class="container container--narrow page-section">
  
<div class="acf-map">
  <?php 
    while(have_posts()) { 
      the_post(); 
      $mapLocation = get_field('map_location');
      ?>
      <!-- print_r($mapLocation) <- zie wat ACF field map_location output. Javascript zal de hoogte- en breedtemeters van de locatie nodig hebben om de API van Google Maps te vertellen waar de punt op Google Maps getoond dient te worden.
      echo $mapLocation['lng'] laat bijvoorbeeld zien wat de coordinatoren zijn van langitude.
      Deze langitufe en latitude worden dus met JavaScript in een Maps weergave op de website getoond, dit deel in JS is niet zelf geschreven, maar geëxporteerd uit Google developer website. -->
     <div class="marker" data-lat="<?php echo $mapLocation['lat'] ?>" data-lng="<?php echo $mapLocation['lng']; ?>"></div>
    <?php }
    echo paginate_links(); //Automatisch toevoegen van volgende en vorige-pagina's indien aantal blog posts meer is dan op het scherm wordt getoond. Dat staat standaard op 10 en kan je aanpassen onder WordPress admin -> Instellingen -> Lezen "Blog pagina's tonen maximaal". Latere toevoeging: dit werkt enkel vanzelf op archive or standaard blog posts of mutaties op blog posts (zoals events in deze cursus zijn). Je moet meer toevoegen als het afgelopen events zijn, zie page-past-events.php.
  ?>
</div>


</div>

<php get_footer();

?>