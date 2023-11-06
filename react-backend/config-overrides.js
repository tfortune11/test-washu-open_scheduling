module.exports = function override(config, env) {

    config.output = {
        ...config.output, // copy all settings
        filename: "static/js/[name].js",
        chunkFilename: "static/js/[name].chunk.js"
    };

    config.plugins.map((plugin, i) => {
        if (plugin.options && plugin.options.filename && plugin.options.filename.includes('static/css')) 
        {
            config.plugins[i].options = {
                ...config.plugins[i].options,
                filename : "static/css/[name].css",
                chunkFilename : "static/css/[name].chunk.css"
            }
        }
    });

    return config;
};