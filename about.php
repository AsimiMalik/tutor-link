
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>About Us | Brilliance</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body{
    font-family:'Poppins',sans-serif;
    background:#f8fafc;
}

.about-hero{
    padding:180px 0 100px;
    background:linear-gradient(
    rgba(15,23,42,.8),
    rgba(15,23,42,.8)),
    url('assets/images/hero-image.png');
    background-size:cover;
    background-position:center;
    color:white;
    text-align:center;
}

.about-hero h1{
    font-size:60px;
    margin-bottom:20px;
}

.about-hero p{
    max-width:700px;
    margin:auto;
    line-height:1.8;
}

.container{
    width:90%;
    max-width:1200px;
    margin:auto;
}

.section{
    padding:100px 0;
}

.story{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:60px;
    align-items:center;
}

.story img{
    width:100%;
    border-radius:25px;
}

.story-text h2{
    font-size:42px;
    margin-bottom:20px;
}

.story-text p{
    line-height:1.9;
    color:#64748b;
}

.values{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:30px;
}

.value-card{
    background:white;
    padding:40px;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(0,0,0,.05);
    text-align:center;
}

.value-card i{
    font-size:40px;
    color:#2563eb;
    margin-bottom:20px;
}

.value-card h3{
    margin-bottom:15px;
}

.value-card p{
    color:#64748b;
}

.stats{
    background:#2563eb;
    color:white;
    padding:100px 0;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    text-align:center;
}

.stats-grid h2{
    font-size:50px;
}

.stats-grid p{
    opacity:.9;
}

.cta{
    padding:100px 0;
}

.cta-box{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    text-align:center;
    padding:80px;
    border-radius:30px;
}

.cta-box h2{
    font-size:48px;
    margin-bottom:20px;
}

.cta-btn{
    display:inline-block;
    margin-top:25px;
    background:#f59e0b;
    color:white;
    padding:15px 35px;
    border-radius:12px;
    text-decoration:none;
}

@media(max-width:900px){

.story{
grid-template-columns:1fr;
}

.values{
grid-template-columns:1fr;
}

.stats-grid{
grid-template-columns:1fr;
gap:30px;
}

.about-hero h1{
font-size:42px;
}

}
:root{
  --primary:#2563eb;
  --primary-dark:#1d4ed8;
  --secondary:#f59e0b;
  --dark:#0f172a;
  --text:#475569;
  --light:#ffffff;
  --bg:#f8fafc;
  --border:#e2e8f0;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  background:var(--bg);
  color:var(--dark);
  overflow-x:hidden;
}

.container{
  width:90%;
  max-width:1300px;
  margin:auto;
}

.navbar{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  background:white;
  z-index:1000;
  box-shadow:0 2px 20px rgba(0,0,0,.05);
}

.nav-container{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 0;
}

.logo{
  display:flex;
  align-items:center;
  gap:10px;
  text-decoration:none;
  color:var(--dark);
  font-size:26px;
  font-weight:700;
}

.logo img{
  width:50px;
}

.accent{
  color:var(--secondary);
}

.nav-links{
  display:flex;
  gap:30px;
  list-style:none;
}

.nav-links a{
  text-decoration:none;
  color:var(--dark);
  font-weight:500;
}

.nav-buttons{
  display:flex;
  gap:12px;
}

.btn-primary,
.cta-btn,
.search-btn{
  background:var(--primary);
  color:white;
  text-decoration:none;
  border:none;
  padding:14px 28px;
  border-radius:12px;
  font-weight:600;
  cursor:pointer;
}

.btn-outline{
  border:2px solid var(--primary);
  color:var(--primary);
  padding:12px 24px;
  border-radius:12px;
  text-decoration:none;
  font-weight:600;
}

.btn-secondary{
  border:2px solid var(--secondary);
  color:var(--secondary);
  text-decoration:none;
  padding:14px 28px;
  border-radius:12px;
  font-weight:600;
}

.hero{
  min-height:100vh;
  padding-top:120px;
  display:flex;
  align-items:center;
  position:relative;
  background:url('assets/images/hero-image.png') center/cover;
}

.hero-overlay{
  position:absolute;
  inset:0;
  background:rgba(15,23,42,.7);
}

.hero-content{
  position:relative;
  z-index:2;
}

.hero-text{
  max-width:700px;
}

.hero h1{
  color:white;
  font-size:70px;
  line-height:1.1;
  margin-bottom:20px;
}

.hero h1 span{
  color:var(--secondary);
}

.hero p{
  color:#e2e8f0;
  line-height:1.8;
  margin-bottom:30px;
}

.hero-badge{
  display:inline-flex;
  gap:10px;
  padding:12px 20px;
  border-radius:50px;
  background:rgba(255,255,255,.15);
  color:white;
  margin-bottom:25px;
}

.hero-buttons{
  display:flex;
  gap:15px;
  margin-bottom:40px;
}

.hero-stats{
  display:flex;
  gap:40px;
}

.stat h3{
  color:white;
}

.stat p{
  color:#cbd5e1;
}

.section-title{
  text-align:center;
  margin-bottom:60px;
}

.section-title h2{
  font-size:48px;
  margin-bottom:15px;
}

.search-section,
.top-tutors,
.why-us,
.cta{
  padding:100px 0;
}

.search-card{
  background:white;
  border-radius:25px;
  padding:25px;
  box-shadow:0 15px 40px rgba(0,0,0,.08);
  display:grid;
  grid-template-columns:2fr 1.5fr 1.5fr auto;
  gap:15px;
}

.search-field{
  display:flex;
  align-items:center;
  gap:12px;
  border:1px solid #e2e8f0;
  border-radius:15px;
  padding:16px;
}

.search-field input{
  width:100%;
  border:none;
  outline:none;
}

.tutor-card{
  background:white;
  border-radius:25px;
  overflow:hidden;
  box-shadow:0 15px 40px rgba(0,0,0,0.06);

  display:flex;
  flex-direction:column;

  text-align:center;
  transition:.3s;

  height:100%;
}

.tutor-card:hover{
  transform:translateY(-10px);
}

.tutor-card img{
  width:100%;
  height:280px;
  object-fit:cover;
}

.tutor-card h3{
  margin:15px 0 8px;
}

.tutor-card p{
  padding:15px 25px;
  color:var(--text);
  line-height:1.7;

  flex-grow:1; /* pushes button downward */
}

.tutor-card .btn-primary{
  margin:20px;
  margin-top:auto;
}
.tutors-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
  align-items:stretch;
}

.subject{
  color:var(--primary);
  font-weight:600;
}

.feature-card i{
  font-size:30px;
  color:var(--primary);
  margin-bottom:15px;
}
  .cta-box{
  background:linear-gradient(
  135deg,
  #2563eb,
  #1e40af
  );

  color:white;
  text-align:center;
  border-radius:35px;

  padding:80px 60px;

  display:flex;
  flex-direction:column;
  align-items:center;
}
.cta-btn{
  margin-top:10px;
}

.btn-primary{
  display:inline-flex;
  align-items:center;
  justify-content:center;
}

.footer{
  background:#021b50;
  color:white;
  padding:80px 0 30px;
}

.footer-grid{
  display:grid;
  grid-template-columns:2fr 1fr 1fr 1fr;
  gap:40px;
}

.footer-logo{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:20px;
}

.footer-logo img{
  width:50px;
}

.footer-links{
  display:flex;
  flex-direction:column;
}

.footer-links h3{
  margin-bottom:20px;
}

.footer-links a{
  color:#cbd5e1;
  text-decoration:none;
  margin-bottom:10px;
}

.footer-bottom{
  text-align:center;
  margin-top:40px;
  padding-top:20px;
  border-top:1px solid rgba(255,255,255,.1);
}

@media(max-width:1000px){

  .search-card,
  .tutors-grid,
  .features-grid,
  .footer-grid{
    grid-template-columns:1fr;
  }

  .nav-links{
    display:none;
  }

  .hero h1{
    font-size:48px;
  }
}
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="about-hero">

<div class="container">

<h1>About Brilliance</h1>

<p>
Connecting Nigerian parents with trusted tutors and helping students achieve academic excellence through quality education.
</p>

</div>

</section>

<section class="section">

<div class="container story">

<img src="assets/images/hero-image.png">

<div class="story-text">

<h2>Our Story</h2>

<p>
Brilliance was founded to solve the challenge many parents face when searching for reliable tutors. We provide a trusted platform where parents can easily discover qualified educators, compare profiles, and make informed decisions.
</p>

</div>

</div>

</section>

<section class="section">

<div class="container">

<div class="values">

<div class="value-card">
<i class="fas fa-bullseye"></i>
<h3>Our Mission</h3>
<p>Make quality tutoring accessible to every student.</p>
</div>

<div class="value-card">
<i class="fas fa-eye"></i>
<h3>Our Vision</h3>
<p>Become Nigeria's most trusted tutor marketplace.</p>
</div>

<div class="value-card">
<i class="fas fa-heart"></i>
<h3>Our Values</h3>
<p>Trust, Excellence, Innovation and Growth.</p>
</div>

</div>

</div>

</section>

<section class="stats">

<div class="container">

<div class="stats-grid">

<div>
<h2>500+</h2>
<p>Tutors</p>
</div>

<div>
<h2>2,000+</h2>
<p>Students</p>
</div>

<div>
<h2>50+</h2>
<p>Subjects</p>
</div>

<div>
<h2>4.9★</h2>
<p>Rating</p>
</div>

</div>

</div>

</section>

<section class="cta">

<div class="container">

<div class="cta-box">

<h2>Ready To Find The Perfect Tutor?</h2>

<p>Join Brilliance today and connect with trusted educators.</p>

<a href="auth/register.php" class="cta-btn">Get Started</a>

</div>

</div>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>