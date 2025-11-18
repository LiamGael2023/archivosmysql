<?php
/**
 * Controlador de autenticación
 */
class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Mostrar formulario de login
     */
    public function loginForm() {
        // Si ya está logueado, redirigir al home
        if ($this->isLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
        unset($_SESSION['login_error']);

        include __DIR__ . '/../views/login.php';
    }

    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor ingrese usuario y contraseña';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Buscar usuario por username o email
        $user = $this->userModel->getByUsername($username);
        if (!$user) {
            $user = $this->userModel->getByEmail($username);
        }

        if (!$user) {
            $_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Verificar si el usuario está activo
        if (!$user['is_active']) {
            $_SESSION['login_error'] = 'Su cuenta está desactivada. Contacte al administrador.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Verificar contraseña
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Login exitoso - crear sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['can_upload'] = $user['can_upload'];
        $_SESSION['can_edit'] = $user['can_edit'];
        $_SESSION['can_delete'] = $user['can_delete'];
        $_SESSION['can_share'] = $user['can_share'];
        $_SESSION['can_manage_users'] = $user['can_manage_users'];

        // Actualizar último login
        $this->userModel->updateLastLogin($user['id']);

        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Verificar si el usuario está logueado
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Requerir autenticación
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Verificar permiso específico
     */
    public static function hasPermission($permission) {
        if (!self::isLoggedIn()) {
            return false;
        }
        return isset($_SESSION[$permission]) && $_SESSION[$permission];
    }

    /**
     * Requerir permiso específico
     */
    public static function requirePermission($permission) {
        self::requireAuth();
        if (!self::hasPermission($permission)) {
            $_SESSION['error'] = 'No tiene permisos para realizar esta acción';
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    /**
     * Obtener usuario actual
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role_id' => $_SESSION['role_id'],
            'role_name' => $_SESSION['role_name'],
            'can_upload' => $_SESSION['can_upload'],
            'can_edit' => $_SESSION['can_edit'],
            'can_delete' => $_SESSION['can_delete'],
            'can_share' => $_SESSION['can_share'],
            'can_manage_users' => $_SESSION['can_manage_users']
        ];
    }
}
