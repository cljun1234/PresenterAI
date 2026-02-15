const mysql = require('mysql2/promise');
const sqlite3 = require('sqlite3').verbose();
const path = require('path');
require('dotenv').config();

const driver = process.env.DB_DRIVER || 'mysql';

let pool;
let db;

if (driver === 'sqlite') {
    const dbPath = path.resolve(__dirname, '../database/database.sqlite');
    db = new sqlite3.Database(dbPath, (err) => {
        if (err) {
            console.error('Could not connect to SQLite database:', err.message);
        } else {
            console.log('Connected to SQLite database.');
        }
    });

    // Promisify SQLite methods for easier use
    db.query = (sql, params = []) => {
        return new Promise((resolve, reject) => {
            db.all(sql, params, (err, rows) => {
                if (err) {
                    reject(err);
                } else {
                    resolve(rows);
                }
            });
        });
    };

    db.execute = (sql, params = []) => {
        return new Promise((resolve, reject) => {
            db.run(sql, params, function (err) {
                if (err) {
                    reject(err);
                } else {
                    // Normalize result to match mysql2 format [rows, fields]
                    // SQLite doesn't return fields in run/all easily, but for basic usage:
                    resolve({ insertId: this.lastID, changes: this.changes });
                }
            });
        });
    };

} else {
    // MySQL
    pool = mysql.createPool({
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'root',
        password: process.env.DB_PASS || '',
        database: process.env.DB_NAME || 'gamma_clone',
        waitForConnections: true,
        connectionLimit: 10,
        queueLimit: 0
    });
}

module.exports = {
    query: async (sql, params) => {
        if (driver === 'sqlite') {
            // Check if it's a SELECT query or modification
            if (sql.trim().toUpperCase().startsWith('SELECT')) {
                const rows = await db.query(sql, params);
                return [rows]; // Return as [rows] to mimic mysql2
            } else {
                const result = await db.execute(sql, params);
                return [result];
            }
        } else {
            return pool.execute(sql, params);
        }
    },
    getConnection: async () => {
        if (driver === 'sqlite') {
            return db;
        } else {
            return pool.getConnection();
        }
    }
};
