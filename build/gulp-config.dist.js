const dotenv = require('dotenv');
dotenv.config();

module.exports = {
	browserConfig: {
		proxy: `http://localhost:8080`,
		port: 3000,
		ui: {
			port: 3001
		}
	},
	folder: {
		www: `${process.env.JOOMLA_FOLDER}`,
		extensions: "../extensions"
	}
}
