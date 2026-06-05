<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contact Us | TutorLink</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body{
font-family:'Poppins',sans-serif;
background:#f8fafc;
}

.contact-hero{
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

.contact-hero h1{
font-size:60px;
margin-bottom:20px;
}

.contact-hero p{
max-width:700px;
margin:auto;
}

.container{
width:90%;
max-width:1200px;
margin:auto;
}

.section{
padding:100px 0;
}

.contact-cards{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:30px;
margin-bottom:80px;
}

.contact-card{
background:white;
padding:40px;
border-radius:25px;
box-shadow:0 15px 40px rgba(0,0,0,.05);
text-align:center;
}

.contact-card i{
font-size:40px;
color:#2563eb;
margin-bottom:20px;
}

.contact-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:50px;
}

.form-box{
background:white;
padding:40px;
border-radius:25px;
box-shadow:0 15px 40px rgba(0,0,0,.05);
}

.form-box input,
.form-box textarea{
width:100%;
padding:15px;
margin-bottom:20px;
border:1px solid #e2e8f0;
border-radius:12px;
}

.form-box textarea{
height:180px;
resize:none;
}

.submit-btn{
background:#2563eb;
border:none;
color:white;
padding:15px 35px;
border-radius:12px;
cursor:pointer;
}

.contact-image img{
width:100%;
border-radius:25px;
height:100%;
object-fit:cover;
}

@media(max-width:900px){

.contact-cards{
grid-template-columns:1fr;
}

.contact-grid{
grid-template-columns:1fr;
}

.contact-hero h1{
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

<section class="contact-hero">

<div class="container">

<h1>Contact TutorLink</h1>

<p>
We'd love to hear from you. Reach out for support, inquiries or partnership opportunities.
</p>

</div>

</section>

<section class="section">

<div class="container">

<div class="contact-cards">

<div class="contact-card">
<i class="fas fa-phone"></i>
<h3>Phone</h3>
<p>+234 800 000 0000</p>
</div>

<div class="contact-card">
<i class="fas fa-envelope"></i>
<h3>Email</h3>
<p>hello@tutorlink.com</p>
</div>

<div class="contact-card">
<i class="fas fa-location-dot"></i>
<h3>Location</h3>
<p>Otukpo, Benue State</p>
</div>

</div>

<div class="contact-grid">

<div class="form-box">

<h2>Send Us A Message</h2>

<br>

<form>

<input type="text" placeholder="Full Name">

<input type="email" placeholder="Email Address">

<input type="text" placeholder="Phone Number">

<input type="text" placeholder="Subject">

<textarea placeholder="Message"></textarea>

<button class="submit-btn">
Send Message
</button>

</form>

</div>

<div class="contact-image">

<img src="assets/images/hero-image.png">

</div>

</div>

</div>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>