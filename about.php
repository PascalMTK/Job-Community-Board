<?php 
session_start();
include('includes/connection.php');
include('includes/functions.php');
include('includes/header.php'); 
?>


<!-- About section starts -->
<section class="about-us">
   <h1 class="heading" data-en="about us" data-af="oor ons">about us</h1>
   <div class="box-container">
      <div class="image">
         <img src="images/account-bg.jpg" alt="People connecting through job opportunities">
      </div>
      <div class="box">
         <h3 data-en="why choose us?" data-af="waarom ons kies?">why choose us?</h3>
         <p>
            Whether you're a student searching for your first opportunity or an experienced employee looking to advance your career,
            <strong data-en="JobHunt" data-af="WerkJag">JobHunt</strong> connects you with the right opportunities. We provide a platform that helps students gain valuable work experience, internships, and part-time jobs that fit their schedules.
         </p>
         <p>
            For professionals and employees, we make career growth easier by featuring trusted companies, verified job offers, and roles suited to your skills and ambitions.
            Our smart search filters and real-time updates ensure that you never miss the perfect match for your next job.
         </p>
         <a href="contact.html" class="btn" data-en="contact us" data-af="kontak ons">contact us</a>
      </div>
   </div>
</section>
<!-- About section ends -->

<!-- Reviews section starts -->
<section class="reviews">
   <h1 class="heading" data-en="top reviews" data-af="top resensies">top reviews</h1>
   <div class="box-container">
      <!-- Review Box Template -->
      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="amazing results" data-af="ongelooflike resultate">amazing results</h3>
         <p>I found an internship within two weeks! JobHunt really made it easy for me to connect with companies looking for students like me.</p>
         <div class="user">
            <img src="images/pic-1.png" alt="Portrait of Nain Bian">
            <div>
               <h3>Nain Bian</h3>
               <span data-en="Computer Science Student" data-af="Rekenaarwetenskap Student">Computer Science Student</span>
            </div>
         </div>
      </div>

      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="easy to use" data-af="maklik om te gebruik">easy to use</h3>
         <p>The platform is simple and smooth to navigate. I applied for several jobs with just one click — perfect for busy students.</p>
         <div class="user">
            <img src="images/pic-2.png" alt="Portrait of Lara Moise">
            <div>
               <h3>Lara Moise</h3>
               <span data-en="Marketing Intern" data-af="Bemarking Intern">Marketing Intern</span>
            </div>
         </div>
      </div>

      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="got selected" data-af="gekies">got selected</h3>
         <p>I uploaded my resume and got an interview offer the next day! Thanks to JobHunt, I landed my first full-time position.</p>
         <div class="user">
            <img src="images/pic-3.png" alt="Portrait of Daniel Khoza">
            <div>
               <h3>Daniel Khoza</h3>
               <span data-en="Junior Web Developer" data-af="Junior Web Ontwikkelaar">Junior Web Developer</span>
            </div>
         </div>
      </div>

      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="nice experience" data-af="lekker ervaring">nice experience</h3>
         <p>The employers here respond quickly, and the job descriptions are clear. It's one of the most reliable job sites I've used.</p>
         <div class="user">
            <img src="images/pic-4.png" alt="Portrait of Faith Moyo">
            <div>
               <h3>Faith Moyo</h3>
               <span data-en="HR Assistant" data-af="HR Assistent">HR Assistant</span>
            </div>
         </div>
      </div>

      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="wide range" data-af="wye reeks">wide range</h3>
         <p>There are so many job options — from part-time to full-time roles. It's great for both students and professionals.</p>
         <div class="user">
            <img src="images/pic-5.png" alt="Portrait of Kevin Banda">
            <div>
               <h3>Kevin Banda</h3>
               <span data-en="Graphic Designer" data-af="Grafiese Ontwerper">Graphic Designer</span>
            </div>
         </div>
      </div>

      <div class="box">
         <div class="stars" aria-label="Rating: 4.5 out of 5 stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
         </div>
         <h3 class="review-title" data-en="super results" data-af="super resultate">super results</h3>
         <p>JobHunt really helped me switch careers smoothly. I'm now working at a company that values my skills and passion.</p>
         <div class="user">
            <img src="images/pic-6.png" alt="Portrait of Anna Nain">
            <div>
               <h3>Anna Nain</h3>
               <span data-en="Software Engineer" data-af="Sagteware Ingenieur">Software Engineer</span>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Reviews section ends -->


<?php include('inlcudes/footer.php')?>
