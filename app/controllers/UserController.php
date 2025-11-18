<?php
/**
 * Controlador de gestión de usuarios
 */
class UserController {
    private $userModel;
    private $roleModel;

    public function __construct() {
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * Mostrar lista de usuarios
     */
    public function index() {
        AuthController::requirePermission('can_manage_users');

        $users = $this->userModel->getAll();
        $roles = $this->roleModel->getAll();
        $currentUser = AuthController::getCurrentUser();

        include __DIR__ . '/../views/users.php';
    }

    /**
     * Crear nuevo usuario
     */
    public function create() {
        AuthController::requirePermission('can_manage_users');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $role_id = (int)($_POST['role_id'] ?? 0);

        // Validaciones
        if (empty($username) || empty($email) || empty($password) || $role_id <= 0) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El email no es válido';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Verificar que no exista el username
        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = 'El nombre de usuario ya existe';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Verificar que no exista el email
        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'El email ya está registrado';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Crear usuario
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $this->userModel->hashPassword($password),
            'full_name' => $full_name,
            'role_id' => $role_id
        ];

        if ($this->userModel->create($data)) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    /**
     * Actualizar usuario
     */
    public function update() {
        AuthController::requirePermission('can_manage_users');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $id = (int)($_POST['user_id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $role_id = (int)($_POST['role_id'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'Usuario no válido';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // No permitir desactivar al admin principal
        if ($id == 1 && !$is_active) {
            $_SESSION['error'] = 'No se puede desactivar al administrador principal';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // No permitir cambiar el rol del admin principal
        if ($id == 1 && $role_id != 1) {
            $_SESSION['error'] = 'No se puede cambiar el rol del administrador principal';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Validaciones
        if (empty($username) || empty($email) || $role_id <= 0) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El email no es válido';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Verificar que no exista el username (excluyendo el actual)
        if ($this->userModel->usernameExists($username, $id)) {
            $_SESSION['error'] = 'El nombre de usuario ya existe';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Verificar que no exista el email (excluyendo el actual)
        if ($this->userModel->emailExists($email, $id)) {
            $_SESSION['error'] = 'El email ya está registrado';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // Preparar datos para actualizar
        $data = [
            'username' => $username,
            'email' => $email,
            'full_name' => $full_name,
            'role_id' => $role_id,
            'is_active' => $is_active
        ];

        // Solo actualizar contraseña si se proporciona una nueva
        if (!empty($password)) {
            $data['password'] = $this->userModel->hashPassword($password);
        }

        if ($this->userModel->update($id, $data)) {
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    /**
     * Eliminar usuario
     */
    public function delete() {
        AuthController::requirePermission('can_manage_users');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $id = (int)($_POST['user_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Usuario no válido';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // No permitir eliminar al admin principal
        if ($id == 1) {
            $_SESSION['error'] = 'No se puede eliminar al administrador principal';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // No permitir eliminarse a sí mismo
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puede eliminarse a sí mismo';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = 'Usuario eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el usuario';
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    /**
     * Obtener datos de usuario (para AJAX)
     */
    public function get() {
        AuthController::requirePermission('can_manage_users');

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['error' => 'Usuario no válido']);
            exit;
        }

        $user = $this->userModel->getById($id);

        if (!$user) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        // No enviar la contraseña
        unset($user['password']);

        header('Content-Type: application/json');
        echo json_encode($user);
        exit;
    }
}
