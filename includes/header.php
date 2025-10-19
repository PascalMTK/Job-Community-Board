<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-en="Home" data-af="Tuis">Home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   
   <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="header">
   <section class="flex">
      <div id="menu-btn" class="fas fa-bars-staggered"></div>
      <a href="home.php" class="logo"><i class="fas fa-user-tie"></i> 
         <span data-en="JobHunt" data-af="WerkJag">JobHunt</span></a>
      <nav class="navbar">
         <a href="home.php" data-en="home" data-af="tuis">home</a>
         <a href="about.php" data-en="about us" data-af="oor ons">about us</a>
         <a href="all_jobs.php" data-en="all jobs" data-af="alle werke">all jobs</a>
         <a href="contact.php" data-en="contact us" data-af="kontak ons">contact us</a>
         
         <?php if (isset($_SESSION['user_id'])): ?>
            <a href="account.php" data-en="account" data-af="rekening">
               <i class="fas fa-user"></i> account
            </a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
               <a href="dashboard.php" data-en="dashboard" data-af="kontroleskerm">dashboard</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'employer'): ?>
               <a href="employer_home.php" data-en="dashboard" data-af="kontroleskerm">dashboard</a>
            <?php endif; ?>
            <a href="logout.php" style="color: #e74c3c;" data-en="logout" data-af="teken uit">
               <i class="fas fa-sign-out-alt"></i> logout
            </a>
         <?php else: ?>
            <a href="login.php" data-en="login" data-af="aanmeld">login</a>
            <a href="register.php" data-en="register" data-af="registreer">register</a>
         <?php endif; ?>
      </nav>
      
      <?php if (isset($_SESSION['user_id'])): ?>
         <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'employer'): ?>
            <a href="employer_home.php#post-job" class="btn" style="margin-top: 0;" data-en="post job" data-af="plaas werk">
               <i class="fas fa-plus"></i> post job
            </a>
         <?php else: ?>
            <a href="#" class="btn" style="margin-top: 0; background: #95a5a6; cursor: not-allowed;" 
               onclick="alert('Only employers can post jobs. Please register as an employer to post job listings.'); return false;" 
               data-en="post job" data-af="plaas werk">
               <i class="fas fa-lock"></i> post job
            </a>
         <?php endif; ?>
      <?php else: ?>
         <a href="login.php" class="btn" style="margin-top: 0;" data-en="post job" data-af="plaas werk">
            <i class="fas fa-briefcase"></i> post job
         </a>
      <?php endif; ?>
   </section>
</header>
