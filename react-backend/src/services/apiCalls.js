function pageUrl()
{
    var treeElement = document.querySelector("#backendApp");
    var pageUrl = treeElement.getAttribute('data-url');
    
    return pageUrl;
}

function getAdminAPI()
{
    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/getAdminAPI/';
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {'Content-Type':'application/json'},
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function postStartBlockData(blockData)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/poststartblockdata/';
    var blockJSON = JSON.stringify(blockData);
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json'  },
            body: JSON.stringify({
                "id": blockData.id,
                "name": blockData.name, 
                "json": blockJSON,
                "treeId": blockData.decisionTree,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function insertUpdateDataAPI(treeJson, treeName, treeId)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/treepostdata/';
    var treeString = JSON.stringify(treeJson);
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json'  },
            body: JSON.stringify({
                "treeJson":treeString,
                "treeName":treeName, 
                "treeId": treeId,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function deleteDataAPI(id)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/treedeletedata/';
    return new Promise((resolve, reject) => { 
        fetch( url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "id": id,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}


//Delete data from an API by Post
export async function deleteStartBlockDataAPI(id)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/startblockdeletedata/';
    return new Promise((resolve, reject) => { 
        fetch( url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "blockId": id,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getDataAPI(id)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/treegetdata';
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "treeId": id,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getStartBlockDataAPI(id)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/startblockgetdata';
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "startBlockId": id,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getStartBlockDataByTree(id)
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/startblockgetbytree';
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "treeId": id,
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) 
            {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getAllStartBlockAPI()
{
    var apiKey = await getAdminAPI();
    
    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/startblockgetalldata';
    return new Promise((resolve, reject) => { 
        fetch(url, 
        {
            method: 'post',
            headers: {
                'Content-Type':'application/json' },
            body: JSON.stringify({
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getDropDownDataAPI()
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/getdropdowndata';
    return new Promise((resolve, reject) => { 
        fetch(url, {
            method: 'post',
            headers: {
                'Content-Type':'application/json',
            },
            body: JSON.stringify({
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}

export async function getAllTreeDataAPI()
{
    var apiKey = await getAdminAPI();

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/treegetalldata';
    return new Promise((resolve, reject) => { 
        fetch(url, {
            method: 'post',
            headers: {
                'Content-Type':'application/json',
            },
            body: JSON.stringify({
                "apiKey": apiKey.API
            })
        }).then((response) => 
        {
            if (response.status === 400) {
                reject(response);
            }
            else
            {
                resolve(response.json());
            }
        });
    });    
}