<?php
get_header();
get_post();
// Template Name: Front Page Test
?>
<style>
    .search-bg {
        /* background: url(https://jobboards.adlogic.com.au/rib-software/wp-content/themes/rib-software/images/banner.png) center; */
        padding: 0;
        background-size: cover !important;
        display: flex;
        align-items: center;
        overflow: hidden;
        background-position-x: right;
        
    }

    .search-bg img {
        width: 100% !important;
        position: relative;
    }

    input.ajb-search-for-jobs-button {
        background: #3DAF2C !important;
        color: #FFFFFF !important;
        font-weight: bold !important;
        border-radius: 2px !important;
    }

    .input.ajb-search-for-jobs-button:hover {
        background: #3DAF2C !important;
        color: #FFFFFF !important;
        border-radius: 2px !important;

    }

    input.ajb-view-all-jobs-button {
        background: #3DAF2C !important;
        color: #FFFFFF !important;
        font-weight: bold !important;
    }

    .input.ajb-view-all-jobs-button:hover {
        background: #3DAF2C !important;
        color: #FFFFFF !important;
    }

    button.btn.btn-success.w-100.mb-2.shadow.rounded {
        background: #FFF !important;
        color: #0033a0 !important;
        border: 1px solid #0033a0;
    }

    @media (max-width: 1486px) {
        .search-bg {
            height: 300px !important;
        }
    }

    @media (max-width: 1098px) {
        .search-bg {
            height: 250px !important;
        }
    }

    @media (max-width: 914px) {
        .search-bg {
            height: 200px !important;
        }
    }

    @media (max-width: 730px) {
        .search-bg {
            height: 170px !important;
        }
    }

    @media (max-width: 616px) {
        .search-bg {
            height: 140px !important;
        }
    }

    @media (max-width: 510px) {
        .search-bg {
            height: 120px !important;
        }
    }

    @media (max-width: 438px) {
        .search-bg {
            height: 100px !important;
        }
    }

    img.banner-img {
        width: 100%;
        margin: auto;
        display: block;
    }

    #privacy-link {
        color: #1a2847 !important;

    }

    #privacy-link:hover {
        color: #fbb03b !important;
    }

    #row-info-list>li {
        font: 14px/1.55em Roboto, Arial, Sans-serif !important;
    }
</style>
<div id="job-board special-bg" class="search-bg">
<div class="bg-fade-dark"></div>
    <div class="intro">
        <h1>Career with Us</h1>
        <h4>The success of our business relies on the skills and dedication of our employees.  Our AESTEC leaders are experienced in all fields of maintenance and project management and we provide ongoing training and support to our team to ensure the highest standards are always achieved.</h4>
        
    </div>
</div>


<div class="container">
    <div class="row head-banner m-0"></div>
    <section id="job-board special-bg">
        <div class="container">
            <!-- <div class="row info-board">
        <div class="col-12">
    
</div>
</div>
    </div> -->
            <!-- <h5 class="text-dark text-center my-4" style="font-size: 3rem;">Current Vacancies</h5> -->
            <div class="intro">
                        <h3>CAREER OPPORTUNITIES</h3>
                    </div>

            <div class="row job-board">

                    

                        <div id="mrp-job-listings" class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                            <?php echo do_shortcode("[adlogic_search_results template='synlait']"); ?>
                            <?php echo do_shortcode("[adlogic_search_pagination]"); ?>
                        </div>
                        <div class="box-sidebar shadow col-md-3 "><?php dynamic_sidebar('search_widget'); ?><br />
                            <div class="box-sidebar-title">
                                <h4>Register your interest</h4>
                            </div>
                            <div class="box-sidebar-content">
                                <p>If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                            </div>
                            <hr>
                            <a type="button" href="" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow rounded text-white">Registration</a>
                        </div>

                        <!-- <div class="box-sidebar shadow">
                            <div class="box-sidebar-title">
                                <h4>Expressions of Interest 2023 Apprenticeships and Traineeships</h4>
                            </div>
                            <div class="box-sidebar-content">
                                <p>Click here to register your interest for 2023 Apprenticeships and Traineeships.</p>
                            </div>
                            <hr>
                            <a type="button" href="https://careers.maasgroup.com.au/job-details/query/1662169/?subSourceId=9999&typeId=job" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow rounded text-white">Registration</a>
                        </div> -->

            </div>
        </div>
    </section>
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