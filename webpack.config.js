const webpack = require("webpack");
const NODE_ENV = process.env.NODE_ENV || "development";
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
	mode: NODE_ENV,
	entry: "./jsrc/index.js",
	output: {
		path: __dirname,
		filename: "./public/assets/js/admin.js",
	},
	module: {
		rules: [
			{
				test: /.js?$/,
				use: [
					{
						loader: "babel-loader",
						options: {
							presets: ["@babel/preset-env"],
							plugins: [
								"@babel/plugin-transform-async-to-generator",
								"@babel/plugin-proposal-object-rest-spread",
								[
									"@babel/plugin-transform-react-jsx",
									{
										pragma: "wp.element.createElement",
									},
								],
								"@babel/plugin-proposal-class-properties"
							],
						},
					},
					"eslint-loader",
				],
				exclude: /node_modules/,
			},
			{
				test: /\.(css|scss)$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					"css-loader",
					{
						loader: "postcss-loader",
						options: {
							plugins: [require("autoprefixer")],
						},
					},
					"sass-loader",
				],
			},
			{
				test: /\.svg$/,
				use: [
					{
						loader: 'svg-url-loader',
						options: {
							limit: 10000,
						},
					},
				],
			}
		],
	},
	plugins: [
		new webpack.DefinePlugin({
			"process.env.NODE_ENV": JSON.stringify(NODE_ENV),
		}),
		new MiniCssExtractPlugin({
			filename: "./public/assets/css/admin.css",
		}),
	],
};
