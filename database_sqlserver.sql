-- Base de datos para sistema de archivos (SQL Server)
-- Crear la base de datos
CREATE DATABASE filemanager_db;
GO

USE filemanager_db;
GO

-- Tabla de carpetas con estructura jerárquica
CREATE TABLE folders (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    parent_id INT NULL,
    created_at DATETIME2 DEFAULT GETDATE(),
    updated_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_folders_parent FOREIGN KEY (parent_id) REFERENCES folders(id) ON DELETE NO ACTION
);
GO

CREATE INDEX idx_folders_parent ON folders(parent_id);
CREATE INDEX idx_folders_name ON folders(name);
GO

-- Tabla de archivos
CREATE TABLE files (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    original_name NVARCHAR(255) NOT NULL,
    file_path NVARCHAR(500) NOT NULL,
    extension NVARCHAR(50) NOT NULL,
    size BIGINT NOT NULL,
    mime_type NVARCHAR(100),
    folder_id INT NULL,
    created_at DATETIME2 DEFAULT GETDATE(),
    updated_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_files_folder FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE
);
GO

CREATE INDEX idx_files_folder ON files(folder_id);
CREATE INDEX idx_files_name ON files(name);
CREATE INDEX idx_files_extension ON files(extension);
CREATE INDEX idx_files_created ON files(created_at);
GO

-- Tabla de metakeys (etiquetas/metadatos)
CREATE TABLE meta_keys (
    id INT IDENTITY(1,1) PRIMARY KEY,
    entity_type NVARCHAR(10) NOT NULL CHECK (entity_type IN ('file', 'folder')),
    entity_id INT NOT NULL,
    meta_key NVARCHAR(100) NOT NULL,
    meta_value NVARCHAR(MAX),
    created_at DATETIME2 DEFAULT GETDATE()
);
GO

CREATE INDEX idx_meta_keys_entity ON meta_keys(entity_type, entity_id);
CREATE INDEX idx_meta_keys_key ON meta_keys(meta_key);
GO

-- Tabla de enlaces compartidos
CREATE TABLE shared_links (
    id INT IDENTITY(1,1) PRIMARY KEY,
    token NVARCHAR(64) NOT NULL UNIQUE,
    entity_type NVARCHAR(10) NOT NULL CHECK (entity_type IN ('file', 'folder')),
    entity_id INT NOT NULL,
    is_active BIT DEFAULT 1,
    expires_at DATETIME2 NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);
GO

CREATE INDEX idx_shared_links_token ON shared_links(token);
CREATE INDEX idx_shared_links_entity ON shared_links(entity_type, entity_id);
CREATE INDEX idx_shared_links_active ON shared_links(is_active);
GO

-- Tabla de roles
CREATE TABLE roles (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(50) NOT NULL UNIQUE,
    description NVARCHAR(255),
    can_upload BIT DEFAULT 0,
    can_edit BIT DEFAULT 0,
    can_delete BIT DEFAULT 0,
    can_share BIT DEFAULT 0,
    can_manage_users BIT DEFAULT 0,
    created_at DATETIME2 DEFAULT GETDATE()
);
GO

-- Tabla de usuarios
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(50) NOT NULL UNIQUE,
    email NVARCHAR(100) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL,
    full_name NVARCHAR(100),
    role_id INT NOT NULL,
    is_active BIT DEFAULT 1,
    last_login DATETIME2 NULL,
    created_at DATETIME2 DEFAULT GETDATE(),
    updated_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);
GO

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role_id);
GO

-- Trigger para actualizar updated_at en folders
CREATE TRIGGER trg_folders_updated
ON folders
AFTER UPDATE
AS
BEGIN
    UPDATE folders
    SET updated_at = GETDATE()
    FROM folders f
    INNER JOIN inserted i ON f.id = i.id;
END;
GO

-- Trigger para actualizar updated_at en files
CREATE TRIGGER trg_files_updated
ON files
AFTER UPDATE
AS
BEGIN
    UPDATE files
    SET updated_at = GETDATE()
    FROM files f
    INNER JOIN inserted i ON f.id = i.id;
END;
GO

-- Trigger para actualizar updated_at en users
CREATE TRIGGER trg_users_updated
ON users
AFTER UPDATE
AS
BEGIN
    UPDATE users
    SET updated_at = GETDATE()
    FROM users u
    INNER JOIN inserted i ON u.id = i.id;
END;
GO

-- Insertar carpeta raíz
SET IDENTITY_INSERT folders ON;
INSERT INTO folders (id, name, parent_id) VALUES (1, 'Root', NULL);
SET IDENTITY_INSERT folders OFF;
GO

-- Insertar roles predeterminados
INSERT INTO roles (name, description, can_upload, can_edit, can_delete, can_share, can_manage_users) VALUES
('admin', 'Administrador - Acceso total al sistema', 1, 1, 1, 1, 1),
('editor', 'Editor - Puede subir, editar, eliminar y compartir', 1, 1, 1, 1, 0),
('collaborator', 'Colaborador - Puede subir y compartir', 1, 0, 0, 1, 0),
('viewer', 'Lector - Solo puede ver y descargar', 0, 0, 0, 0, 0);
GO

-- Insertar usuario administrador por defecto (password: admin123)
INSERT INTO users (username, email, password, full_name, role_id) VALUES
('admin', 'admin@sistema.com', '$2y$12$V2f6AWtrs0uHecbMBzW1SOOoChfGT0SKJ1/NRz2sAXq3ducp61K6S', 'Administrador', 1);
GO
