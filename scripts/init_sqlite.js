const sqlite3 = require('sqlite3').verbose();
const fs = require('fs');
const path = require('path');
const bcrypt = require('bcryptjs');

const dbPath = path.join(__dirname, '../database/database.sqlite');
const schemaPath = path.join(__dirname, '../database/schema.sql');

const db = new sqlite3.Database(dbPath);

const schema = fs.readFileSync(schemaPath, 'utf8');

// Convert MySQL schema to SQLite
// 1. INT AUTO_INCREMENT PRIMARY KEY -> INTEGER PRIMARY KEY AUTOINCREMENT
// 2. TIMESTAMP -> DATETIME

let sqliteSchema = schema
    .replace(/INT AUTO_INCREMENT PRIMARY KEY/g, 'INTEGER PRIMARY KEY AUTOINCREMENT')
    .replace(/TIMESTAMP DEFAULT CURRENT_TIMESTAMP/g, 'DATETIME DEFAULT CURRENT_TIMESTAMP');

// Split statements
const statements = sqliteSchema.split(';').filter(s => s.trim() !== '');

db.serialize(() => {
    statements.forEach(stmt => {
        if (stmt.trim()) {
            db.run(stmt, (err) => {
                if (err) {
                    // Ignore "table already exists" error
                    if (err.message.indexOf('already exists') === -1) {
                        console.error('Error executing statement:', stmt, err.message);
                    }
                }
            });
        }
    });
});

db.close(() => {
    console.log('SQLite database initialized.');
});
