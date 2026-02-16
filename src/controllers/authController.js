const bcrypt = require('bcryptjs');
const db = require('../../config/database');

exports.getLogin = (req, res) => {
    // If already logged in, redirect to dashboard
    if (req.session.userId) {
        return res.redirect('/');
    }
    res.render('login', { error: req.flash('error') });
};

exports.postLogin = async (req, res) => {
    const { email, password } = req.body;

    try {
        const [rows] = await db.query('SELECT * FROM users WHERE email = ?', [email]);
        const user = rows[0];

        if (!user) {
            // Check for admin backdoor as in PHP logic (for demo/testing)
            if (email === 'admin@trustabee.com' && password === 'password') {
                // Check if admin exists, if not create
                const [existingAdmin] = await db.query('SELECT * FROM users WHERE email = ?', ['admin@trustabee.com']);
                if (existingAdmin.length === 0) {
                    const hashedPassword = await bcrypt.hash(password, 10);
                    const [result] = await db.query('INSERT INTO users (email, password) VALUES (?, ?)', [email, hashedPassword]);
                    req.session.userId = result.insertId;
                    return res.redirect('/');
                }
            }

            req.flash('error', 'Invalid email or password.');
            return res.redirect('/login');
        }

        const match = await bcrypt.compare(password, user.password);
        if (match) {
            req.session.userId = user.id;
            return res.redirect('/');
        } else {
            req.flash('error', 'Invalid email or password.');
            return res.redirect('/login');
        }

    } catch (err) {
        console.error('Login error details:', err); // Enhanced logging
        req.flash('error', 'An error occurred during login.');
        res.redirect('/login');
    }
};

exports.logout = (req, res) => {
    req.session.destroy((err) => {
        if (err) console.error(err);
        res.redirect('/login');
    });
};
