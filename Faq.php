<?php /* Template Name: Faq */ ?>
<?php get_header();?>

<main class="main-site">

      <section class="inner-banner">
        <div class="container">
          <div class="banner-inner-main">
            <h2>Faq</h2>
            <ul class="pagin">
              <li><a href="#"><i class="fa-solid fa-house"></i>Home</a></li>
              <li><i class="fa-solid fa-chevron-right"></i></li>
              <li><a href="#">Faq</a></li>
            </ul>
          </div>
        </div>
      </section>

      <section class="About-sec py-100">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="about-info">
                <h3>FAQ's <br> (Frequently Asked Questions)</h3>
                <p>Here are a few of the questions we get the most. If you don't see what's on your mind, reach out to us anytime on phone or email. We are happy to help answer your questions!</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="about-img-sec">
              <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2024/11/faq.jpg' ) ); ?>" alt="">
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="faq-sec py-60">
        <div class="container">
          <div class="accordion" id="accordionExample">
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Accordion Item #1
                  </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Accordion Item #2
                  </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Accordion Item #3
                  </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                  </div>
                </div>
              </div>
            </div>
        </div>
      </section>


    </main>


  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="<?php echo esc_url( home_url( '/ wp-content/uploads/2024/11/coll-e2.png' ) ); ?>" alt="">
      </div>
    </div>
  </div>
</div>
<?php get_footer();?>