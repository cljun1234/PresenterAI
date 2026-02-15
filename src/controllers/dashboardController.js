const db = require('../../config/database');

exports.getIndex = async (req, res) => {
    try {
        const [presentations] = await db.query('SELECT * FROM presentations WHERE user_id = ? ORDER BY created_at DESC', [req.session.userId]);

        const data = {
            presentations: presentations,
            user: req.session.userId,
            path: '/'
        };

        if (presentations.length === 0) {
            res.render('dashboard_empty', data);
        } else {
            res.render('dashboard_list', data);
        }
    } catch (err) {
        console.error(err);
        res.status(500).send('Database error');
    }
};

exports.getCreate = (req, res) => {
    res.render('create', { user: req.session.userId, path: '/create' });
};

exports.getTemplates = (req, res) => {
    res.render('templates', { user: req.session.userId, path: '/templates' });
};

exports.getThemes = (req, res) => {
    res.render('themes', { user: req.session.userId, path: '/themes' });
};

exports.getFonts = (req, res) => {
    res.render('fonts', { user: req.session.userId, path: '/fonts' });
};

exports.getTrash = (req, res) => {
    res.render('trash', { user: req.session.userId, path: '/trash' });
};
