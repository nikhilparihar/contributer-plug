<?php get_header(); ?>
<div class="wrap">
<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<!-- This sets the $curauth variable -->

    <?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    ?>

    <h2>About: <?php echo $curauth->nickname; ?></h2>
    <dl>
        <dt>Website</dt>
        <dd><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></dd>
        <dt>Profile</dt>
        <dd><?php echo $curauth->user_description; ?></dd>
    </dl>

    <h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

    <ul style="list-style:none">
<!-- The Loop -->

<?php
global $wpdb;
$post=array();
$sql=$wpdb->get_results("select * from ".$wpdb->prefix."posts where post_type='post' AND post_name!=''");
foreach($sql as $key=>$sqls){
$contributer=get_post_meta($sqls->ID,'contributer');
$cont_id=explode(",",$contributer[0]);
foreach($cont_id as $cid ){
if($cid==$curauth->ID){
$post[$key]=$sqls->ID;
}
}
}
//echo '<pre>';print_r($post);die;
foreach($post as $posts){

$data=get_post($posts);
//echo '<pre>';print_r($data);

    ?>
        <li style="border: 1px solid gray;padding: 10px;border-radius: 5px;margin:5px">
            <a href="<?php echo $data->guid ?>" rel="bookmark" title="Permanent Link: <?php echo $data->post_title; ?>"><h1>
            <?php echo $data->post_title; ?></h1></a>
            <?php echo  $data->post_date; ?> 
            <div><?php echo  $data->post_content; ?></div>
        </li>

    
    <?php } 
 ?>

<!-- End Loop -->

    </ul>
    <?php if(empty($post)){ ?>
	<div><h3>No post found by this author.</h3></div>
	
	<?php } ?>
		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer(); ?> 