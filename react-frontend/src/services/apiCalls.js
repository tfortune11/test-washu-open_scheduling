//To shortent the URL, this funciton is used to allow us to use it in a function
export function pageUrl()
{
    var treeElement = document.querySelector("#frontendApp");
    var pageUrl = treeElement.getAttribute('data-url');
    
    return pageUrl;
}

//Get Frontend API
async function getFrontendAPI()
{
  var frontDate = localStorage.getItem('frontendDate');
  var today = new Date();

  if(frontDate)
  {
      if(frontDate != today.getDate())
      {
        var apiKey = await runFrontendAPI();
        localStorage.setItem('frontendAPI', JSON.stringify(apiKey));
        localStorage.setItem('frontendDate', today.getDate());
      }
  }
  else
  {
    var apiKey = await runFrontendAPI();
    localStorage.setItem('frontendAPI', JSON.stringify(apiKey));
    localStorage.setItem('frontendDate', today.getDate());
  }

  var frontAPI = localStorage.getItem('frontendAPI');
  
  if(frontAPI)
  {
    return JSON.parse(frontAPI);
  }
  else
  {
    var apiKey = await runFrontendAPI();
    localStorage.setItem('frontendAPI', JSON.stringify(apiKey));
    localStorage.setItem('frontendDate', today.getDate());

    return apiKey;
  }
}

async function runFrontendAPI()
{
    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/getFrontendAPI/';
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

export async function getDataAPI(id)
{
    var apiKey = await getFrontendAPI();

    var url =  pageUrl() + '/wp-json/washu-open-scheduling/v1/treegetdata';
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

export async function getStartBlockDataByTree(id)
{
    var apiKey = await getFrontendAPI();

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

export async function getDropDownDataAPI()
{
    var apiKey = await getFrontendAPI();

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

export async function saveUserJourney(id, startBlockId, startBlock, decisionTreeId, decisionTree, question, answer, termination)
{
    var apiKey = await getFrontendAPI();

    const locationRequest = await fetch('https://geolocation-db.com/json/');
    const locationData = await locationRequest.json();

    var country = (locationData && locationData.country_code ? locationData.country_code : "N/A");
    var countryName = (locationData && locationData.country_name ? locationData.country_name : "N/A");
    var city = (locationData && locationData.city ? locationData.city : "N/A");
    var state = (locationData && locationData.state ? locationData.state : "N/A");
    var postal = (locationData && locationData.postal ? locationData.postal : "N/A");
    var longitude = (locationData && locationData.latitude ? locationData.latitude : "N/A");
    var latitude = (locationData && locationData.longitude ? locationData.longitude : "N/A");
    var ipAddress = (locationData && locationData.IPv4 ? locationData.IPv4 : "N/A");

    var url = pageUrl() + '/wp-json/washu-open-scheduling/v1/track_users';

    return new Promise((resolve, reject) => { 
        fetch(url, {
            method: 'post',
            headers: {
                'Content-Type':'application/json',
            },
            body: JSON.stringify({
                "sessionID": id,
                "country": country,
                "countryName": countryName,
                "city": city,
                "state": state,
                "postal": postal,
                "longitude": longitude,
                "latitude": latitude,
                "ipAddress": ipAddress,
                "browserInfo": navigator.userAgent,
                "startBlockID": startBlockId,
                "startBlock": startBlock,
                "decisionTreeID": decisionTreeId,
                "decisionTree": decisionTree,
                "question": question,
                "answer": answer,
                "termination": termination,
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
