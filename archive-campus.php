<?php

get_header();
page_banner(array(
  'title' => 'Our Campuses',
  'subtitle' => 'We have several conveniently located campuses.'
));
?>
  
  
  <div class="container container--narrow page-section">
    <div class="acf-map">
        <?php while (have_posts()) {
          the_post(); 
          $mapLocation = get_field('map_location')
        ?>
            <div 
              data-lat="<?php echo $mapLocation['lat']; ?>" 
              data-lng="<?php echo $mapLocation['lng']; ?>" 
              class="marker"
            >
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <p><?php echo $mapLocation['address']; ?></p>
            </div>

        <?php
        }
        
        ?>
    </div>

  </div>

<?php
get_footer();

?>