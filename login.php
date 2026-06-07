<?php
// =====================================================
// NexaFlow – Page Connexion / Inscription
// =====================================================
require_once 'includes/functions.php';

if (isLoggedIn()) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'chef_projet') {
        header('Location: manager_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error   = '';
$success = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            $result = login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($result['success']) {
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'chef_projet') {
                    header('Location: manager_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = $result['message'];
            }
        } elseif ($_POST['action'] === 'register') {
            $result = register([
                'firstname'    => $_POST['firstname'] ?? '',
                'lastname'     => $_POST['lastname'] ?? '',
                'email'        => $_POST['email'] ?? '',
                'password'     => $_POST['password'] ?? '',
                'role'         => $_POST['role'] ?? 'developpeur',
            ]);
            if ($result['success']) {
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'chef_projet') {
                    header('Location: manager_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Message d'erreur GET (ex: session expirée)
if (isset($_GET['error'])) {
    $errorMsgs = [
        'session_expired'  => 'Votre session a expirée. Veuillez vous reconnecter.',
        'session_timeout'  => 'Session expirée pour inactivité. Reconnectez-vous.',
        'unauthorized'     => 'Accès non autorisé.',
        'account_disabled' => 'Votre compte n\'existe plus ou a été désactivé.',
    ];
    $error = $errorMsgs[$_GET['error']] ?? 'Une erreur est survenue.';
}

$defaultTab = $_GET['tab'] ?? 'login';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NexaFlow – Plateforme Intégrative de Gestion de Projet</title>
  <meta name="description" content="NexaFlow est une plateforme de gestion de projet unifiée avec des outils d'intégration avancés pour les équipes modernes." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/login.css" />
</head>
<body class="login-page">

  <!-- Animated background -->
  <div class="bg-animated">
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>
    <canvas id="networkCanvas"></canvas>
  </div>

  <!-- Bouton de retour en haut à gauche -->
  <a href="index.php" style="position: absolute; top: 30px; left: 30px; z-index: 100; display:inline-flex; align-items:center; padding:10px 20px; background-color:#3b82f6; color:white; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; transition:all 0.3s; box-shadow:0 4px 14px rgba(59,130,246,0.3);" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Retour à l'accueil
  </a>

  <!-- Wrapper for centering -->
  <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; z-index: 10;">
    
    <!-- Login Container -->
    <div class="login-container">

      <!-- ─── CENTER PANEL ────────────────────────── -->
      <div class="login-right">
        <div class="login-card" style="position:relative;">

          <?php if (!empty($error)): ?>
        <div class="alert alert-error" id="globalAlert">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <div class="login-tabs">
          <button class="tab-btn <?= $defaultTab === 'login' ? 'active' : '' ?>" id="loginTab" onclick="switchTab('login')">Connexion</button>
          <button class="tab-btn <?= $defaultTab === 'register' ? 'active' : '' ?>" id="registerTab" onclick="switchTab('register')">Inscription</button>
        </div>

        <!-- ── CONNEXION ── -->
        <form id="loginForm" class="auth-form <?= $defaultTab === 'login' ? 'active' : '' ?>"
              method="POST" action="login.php" onsubmit="showLoader(this)">
          <input type="hidden" name="action" value="login" />
          <h2>Bienvenue</h2>
          <p class="form-subtitle">Connectez-vous à votre espace de travail</p>

          <div class="form-group">
            <label for="loginEmail">Adresse email</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </span>
              <input type="email" id="loginEmail" name="email"
                     placeholder="vous@exemple.com" required
                     value="<?= isset($_POST['email']) && $_POST['action'] === 'login' ? htmlspecialchars($_POST['email']) : '' ?>" />
            </div>
          </div>

          <div class="form-group">
            <label for="loginPassword">Mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <input type="password" id="loginPassword" name="password"
                     placeholder="••••••••" required />
              <button type="button" class="toggle-pwd" onclick="togglePassword('loginPassword', this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="form-options">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" id="rememberMe" />
              <span class="checkmark"></span>
              Se souvenir de moi
            </label>
            <a href="#" class="forgot-link">Mot de passe oublié?</a>
          </div>

          <button type="submit" class="btn-primary" id="loginBtn">
            <span class="btn-text">Se connecter</span>
            <span class="btn-loader hidden"></span>
          </button>

          <div class="divider"><span>ou continuer avec</span></div>
          <div class="social-btns">
            <button type="button" class="btn-social" onclick="showToast('Intégration OAuth à configurer', 'info')">
              <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
              Google
            </button>
            <button type="button" class="btn-social" onclick="showToast('Intégration OAuth à configurer', 'info')">
              <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#F25022" d="M1 1h10v10H1z"/><path fill="#00A4EF" d="M13 1h10v10H13z"/><path fill="#7FBA00" d="M1 13h10v10H1z"/><path fill="#FFB900" d="M13 13h10v10H13z"/></svg>
              Microsoft
            </button>
          </div>
        </form>

        <!-- ── INSCRIPTION ── -->
        <form id="registerForm" class="auth-form <?= $defaultTab === 'register' ? 'active' : '' ?>"
              method="POST" action="login.php" onsubmit="showLoader(this)">
          <input type="hidden" name="action" value="register" />
          <h2>Créer un compte</h2>
          <p class="form-subtitle">Rejoignez des milliers d'équipes productives</p>

          <div class="row row-cols-1 row-cols-md-2 g-3">
            <div class="form-group">
              <label for="regFirstname">Prénom</label>
              <div class="input-wrapper">
                <span class="input-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <input type="text" id="regFirstname" name="firstname" placeholder="Jean" required
                       value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>" />
              </div>
            </div>
            <div class="form-group">
              <label for="regLastname">Nom</label>
              <div class="input-wrapper">
                <span class="input-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <input type="text" id="regLastname" name="lastname" placeholder="Dupont" required
                       value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="regEmail">Adresse email</label>
            <div class="input-wrapper">
              <span class="input-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
              <input type="email" id="regEmail" name="email" placeholder="vous@exemple.com" required
                     value="<?= isset($_POST['email']) && $_POST['action'] === 'register' ? htmlspecialchars($_POST['email']) : '' ?>" />
            </div>
          </div>

          <div class="form-group">
            <label for="regRole">Vous inscrire en tant que</label>
            <div class="input-wrapper">
              <span class="input-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
              <select id="regRole" name="role" required style="width:100%; height:48px; padding-left:42px; padding-right:16px; background:rgba(10,22,40,0.7); border:1px solid rgba(59,130,246,0.2); border-radius:10px; outline:none; font-family:'Inter',sans-serif; color:white; appearance:none; transition:all 0.3s;">
                <option value="developpeur" style="background:#0a1628; color:white; padding:10px;" <?= (isset($_POST['role']) && $_POST['role'] === 'developpeur') ? 'selected' : '' ?>>Utilisateur / Membre d'équipe</option>
                <option value="chef_projet" style="background:#0a1628; color:white; padding:10px;" <?= (isset($_POST['role']) && $_POST['role'] === 'chef_projet') ? 'selected' : '' ?>>Chef de projet</option>
              </select>
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute; right:16px; top:17px; pointer-events:none; color:var(--gray-500)"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>

          <div class="form-group">
            <label for="regPassword">Mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              <input type="password" id="regPassword" name="password"
                     placeholder="Min. 8 caractères" required minlength="8"
                     oninput="checkPasswordStrength(this.value)" />
              <button type="button" class="toggle-pwd" onclick="togglePassword('regPassword', this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="password-strength" id="pwdStrength">
              <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
              <span class="strength-text" id="strengthText"></span>
            </div>
          </div>

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" id="termsAccept" required />
              <span class="checkmark"></span>
              J'accepte les <a href="#" class="link">conditions d'utilisation</a> et la <a href="#" class="link">politique de confidentialité</a>
            </label>
          </div>

          <button type="submit" class="btn-primary">
            <span class="btn-text">Créer mon compte</span>
            <span class="btn-loader hidden"></span>
          </button>
        </form>

      </div>
    </div>
  </div>
  </div> <!-- Fin Wrapper -->

  <div class="toast" id="toast"></div>

  <script src="assets/js/network-bg.js"></script>
  <script src="assets/js/auth.js"></script>
  <script>
    // Ouvrir le bon onglet si erreur sur register
    <?php if ($defaultTab === 'register' || (isset($_POST['action']) && $_POST['action'] === 'register' && !empty($error))): ?>
    document.addEventListener('DOMContentLoaded', () => switchTab('register'));
    <?php endif; ?>
  </script>
</body>
</html>



