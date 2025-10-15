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

<?php include('inlcudes/footer.php')?>
