const express = require('express');
const router = express.Router();
const dashboardController = require('../controllers/dashboardController');

// All routes here should be protected, middleware can be added here or in controller
const requireAuth = (req, res, next) => {
    if (!req.session.userId) {
        return res.redirect('/login');
    }
    next();
};

router.get('/', requireAuth, dashboardController.getIndex);
router.get('/create', requireAuth, dashboardController.getCreate);
router.get('/templates', requireAuth, dashboardController.getTemplates);
router.get('/themes', requireAuth, dashboardController.getThemes);
router.get('/fonts', requireAuth, dashboardController.getFonts);
router.get('/trash', requireAuth, dashboardController.getTrash);

module.exports = router;
