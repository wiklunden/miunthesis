<?php

class Settings {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = new Database();
        $this->pdo = $this->db->getPdo();
    }
    
   public function getAllSettings() {
        $stmt = $this->pdo->prepare('SELECT * FROM settings');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSetting($name) {
        $stmt = $this->pdo->prepare('SELECT * FROM settings WHERE name = :name');
        $stmt->execute(['name' => $name]);
        return $stmt->fetch();
    }
    
    public function addSetting($name, $value) {
        $setting = $this->getSetting($name);

        if (!$setting) {
            $stmt = $this->pdo->prepare('INSERT INTO settings(name, value) VALUES(:name, :value)');
            $stmt->execute([
                'name' => $name,
                'value' => $value
            ]);
            return true;
        } else {
            throw new Exception('The setting you are trying to add already exists.');
        }
    }
}