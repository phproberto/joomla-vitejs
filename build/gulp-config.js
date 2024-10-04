const dotenv = require('dotenv');
dotenv.config();

module.exports = {
	browserConfig: {
		proxy: `http://localhost:80`,
		port: 3100,
		ui: {
			port: 3101
		}
	},
	folder: {
		www: `/Users/rsegura/www/joomla-cms`,
		extensions: "../extensions"
	}
}
