<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    public function login(): void
    {
        if (isLoggedIn()) {
            $this->redirect('home');
        }

        $this->render('auth/login', [
            'title' => 'Iniciar Sesión',
            'error' => $_SESSION['flash_error'] ?? null
        ]);
        unset($_SESSION['flash_error']);
    }

    public function authenticate(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('login');
    }

    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');   // ← trim agregado

    if (empty($usuario) || empty($password)) {
        $_SESSION['flash_error'] = 'Usuario y contraseña son obligatorios.';
        $this->redirect('login');
    }

    $accountModel = new Account($this->db);
    $user = $accountModel->findByUsuario($usuario);

    if (!$user) {
        $_SESSION['flash_error'] = 'Usuario no encontrado.';
        $this->redirect('login');
    }

    // DEBUG mejorado
    $isValidHash = password_get_info($user['password'])['algo'] !== 0;
    
    if (!$isValidHash) {
        $_SESSION['flash_error'] = 'Error interno: Hash de contraseña inválido.';
        $this->redirect('login');
    }
    // DEBUG DEFINITIVO - quítalo después de probar
file_put_contents(__DIR__ . '/../../debug_login.txt', 
    date('Y-m-d H:i:s') . "\n" .
    "Usuario: " . $usuario . "\n" .
    "Password ingresado: " . $password . "\n" .
    "Hash en BD: " . $user['password'] . "\n" .
    "password_verify() devuelve: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "\n" .
    "------------------------\n\n", 
    FILE_APPEND);
    // Verificación real
    if (password_verify($password, $user['password'])) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol']     = $user['rol'];

        // Éxito
        if ($user['rol'] === 'admin') {
            $this->redirect('admin/dashboard');
        }
        $this->redirect('home');
    }

    // Falló la verificación
    $_SESSION['flash_error'] = 'Usuario o contraseña incorrectos.';
    $this->redirect('login');
}

    public function register(): void
    {
        $this->render('auth/register', ['title' => 'Registro de Cliente']);
    }

    public function registerProcess(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('register');
        }

        $ci         = trim($_POST['ci'] ?? '');
        $usuario    = trim($_POST['usuario'] ?? '');
        $nombres    = trim($_POST['nombres'] ?? '');
        $apPaterno  = trim($_POST['apPaterno'] ?? '');
        $apMaterno  = trim($_POST['apMaterno'] ?? '');
        $correo     = trim($_POST['correo'] ?? '');
        $direccion  = trim($_POST['direccion'] ?? '');
        $nroCelular = trim($_POST['nroCelular'] ?? '');
        $password   = trim($_POST['password'] ?? '');

        if (empty($ci) || empty($usuario) || empty($nombres) || empty($apPaterno) || empty($correo) || empty($direccion) || empty($nroCelular) || empty($password)) {
            $this->render('auth/register', [
                'title' => 'Registro de Cliente',
                'error' => 'Todos los campos marcados son obligatorios.'
            ]);
            return;
        }

        $accountModel = new Account($this->db);
        if ($accountModel->findByUsuario($usuario)) {
            $this->render('auth/register', [
                'title' => 'Registro de Cliente',
                'error' => 'El nombre de usuario ya existe. Elige otro diferente.'
            ]);
            return;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/register', [
                'title' => 'Registro de Cliente',
                'error' => 'El formato de correo electrónico no es válido.'
            ]);
            return;
        }

        $clientModel = new Client($this->db);
        if ($clientModel->findByCi($ci)) {
            $this->render('auth/register', [
                'title' => 'Registro de Cliente',
                'error' => 'La C.I. ya está registrada en el sistema.'
            ]);
            return;
        }

        try {
            $this->db->beginTransaction();

            $createdAccount = $accountModel->create($usuario, $password);
            if (!$createdAccount) {
                throw new RuntimeException('No se pudo crear la cuenta.');
            }

            $createdClient = $clientModel->create([
                'ci' => $ci,
                'nombres' => $nombres,
                'apPaterno' => $apPaterno,
                'apMaterno' => $apMaterno,
                'correo' => $correo,
                'direccion' => $direccion,
                'nroCelular' => $nroCelular,
                'usuarioCuenta' => $usuario
            ]);

            if (!$createdClient) {
                throw new RuntimeException('No se pudo crear el cliente.');
            }

            $this->db->commit();
            $_SESSION['flash_error'] = 'Registro exitoso. Ya puedes iniciar sesión.';
            $this->redirect('login');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $this->render('auth/register', [
                'title' => 'Registro de Cliente',
                'error' => 'Error al registrar el cliente: ' . $e->getMessage()
            ]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('home');
    }
}