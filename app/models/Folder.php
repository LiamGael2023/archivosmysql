<?php
/**
 * Modelo Folder - Gestiona las operaciones de carpetas
 */
class Folder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear una nueva carpeta
     */
    public function create($name, $parent_id = null) {
        $sql = "INSERT INTO folders (name, parent_id) VALUES (:name, :parent_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'parent_id' => $parent_id
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener carpeta por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM folders WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todas las carpetas hijas de un padre
     */
    public function getByParent($parent_id = null) {
        $sql = "SELECT * FROM folders WHERE parent_id " .
               ($parent_id === null ? "IS NULL" : "= :parent_id") .
               " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        if ($parent_id !== null) {
            $stmt->execute(['parent_id' => $parent_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Obtener el árbol completo de carpetas
     */
    public function getTree($parent_id = null, $level = 0) {
        $folders = $this->getByParent($parent_id);
        $tree = [];

        foreach ($folders as $folder) {
            $folder['level'] = $level;
            $folder['children'] = $this->getTree($folder['id'], $level + 1);
            $tree[] = $folder;
        }

        return $tree;
    }

    /**
     * Obtener la ruta completa de una carpeta (breadcrumb)
     */
    public function getPath($id) {
        $path = [];
        $current_id = $id;

        while ($current_id !== null) {
            $folder = $this->getById($current_id);
            if ($folder) {
                array_unshift($path, $folder);
                $current_id = $folder['parent_id'];
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * Actualizar nombre de carpeta
     */
    public function updateName($id, $name) {
        $sql = "UPDATE folders SET name = :name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name
        ]);
    }

    /**
     * Eliminar carpeta (cascade elimina subcarpetas y archivos)
     */
    public function delete($id) {
        $sql = "DELETE FROM folders WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Obtener todas las carpetas
     */
    public function getAll() {
        $sql = "SELECT * FROM folders ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
