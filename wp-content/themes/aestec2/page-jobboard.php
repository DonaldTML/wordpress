<?php
get_header();
get_post();
// Template Name: Front Page
?>

<div class="banner">
    <div class="banner-content">
        <h1>Career with Us</h1>
        <h4 class="banner-h4-top">The success of our business relies on the skills and dedication of our employees.  Our AESTEC leaders are experienced in all fields of maintenance and project management and we provide ongoing training and support to our team to ensure the highest standards are always achieved.</h4>
    </div>
</div>

<div class="job-listing">
    <div class="container">
    <div class="intro">
        <h3>CAREER OPPORTUNITIES</h3>
    </div>
        <div class="row job-board">
            <div class="col-md-6 page-central">
                <?php echo do_shortcode("[adlogic_search_results template='synlait']"); ?>
                <?php echo do_shortcode("[adlogic_search_pagination]"); ?>
            </div>

            <div class="col-md-6 sidebar"><?php dynamic_sidebar('search_widget'); ?><br />
                <div class="eoi">
                    <h2 class="bebas" style="font-size: 22px;">Register your interest</h2>
                    <p class="abril"> If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                    <a class="ajb-subscribe-job-alerts" style="float:left; text-decoration: none;" href="" target="_blank">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ajb_social_sharing_sites,
    .share-text {
        display: none !important;
    }

    .position .date {
        margin: 0px 0px 5px 0px !important;
    }
</style>
<script>
        const targetNode = document.getElementById("mrp-job-listings");

        const config = {
            attributes: true,
            childList: true,
            subtree: true
        };

        // Callback function to execute when mutations are observed
        const callback = function(mutationsList, observer) {
            oldHTML = document.getElementsByClassName("adlogic_job_results");
            oldHTML = oldHTML[0].innerHTML;
            newHTML = "<div class='row inline-row'>" + oldHTML + "</div>";
            document.getElementsByClassName("adlogic_job_results")[0].innerHTML = newHTML;
            observer.disconnect();
            observer.observe(targetNode, config);
        };

        const observer = new MutationObserver(callback);

        observer.observe(targetNode, config);
    </script>
<?php
get_footer();
?>