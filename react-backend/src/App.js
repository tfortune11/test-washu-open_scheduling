import './App.css';
import React, { Component } from "react";
import SortableTree, { toggleExpandedForAll,addNodeUnderParent, changeNodeAtPath, insertNode, removeNodeAtPath, getVisibleNodeCount  } from 'react-sortable-tree';
import 'react-sortable-tree/style.css';
import {getDataAPI,
        postStartBlockData,
        getStartBlockDataAPI, 
        getStartBlockDataByTree, 
        deleteStartBlockDataAPI, 
        getAllStartBlockAPI, 
        getAllTreeDataAPI, 
        insertUpdateDataAPI, 
        deleteDataAPI, 
        getDropDownDataAPI
    } from './services/apiCalls';
import TextNode from './components/node-text';
import QuestionNode from './components/node-question';
import AnswerNode from './components/node-answer';
import TerminateNode from './components/node-termination';
import DTTable from './components/dt-table.js';
import DTPreview from './components/dt-preview';
import StartBlockForm from './components/start-block-form.js';
import StartBlockTable from './components/start-block-table.js';
import ReactModal from 'react-modal';
import { CSVLink, CSVDownload } from "react-csv";

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      appType: "",
      showModalDT: false,
      searchString: '',
      showTreePage: 'start',
      treeName: '',
      treeId:0,
      searchFocusIndex: 0,
      currentNode: {},
      treeList:[],
      subTitle: "",
      treeData: [],
      epicEndpointUrl: '',
      epicAPIKey: '',
      pluginData: "",
      name: "",
      text: "",
      userUrl:"",
      saveMessage: "",
      failMessage:"",
      allDropdownDataState: [],
      academicList:[],
      epicList:[],
      visitTypeList:[],
      providerList:[],
      numTermination:0,

      blockData: {
        id: 0,
        name:"",
        title:"",
        colFormat: "",
        picURL: "",
        leftContentTop:"",
        leftBtnName:"",
        leftBtnLink:"",
        leftContentBottom:"",
        decisionTree:"",
        rightContentTop:"",
        rightBtnName:"",
        rightBtnLink:"",
        rightContentBottom:"",
        centerBottom:""
      },
      blockList: [],

      exportTreeData: [],
      exportBlockData: [],
      
      openSBPreview:false,
    };

    this.handleOpenModalDT = this.handleOpenModalDT.bind(this);
    this.handleCloseModalDT = this.handleCloseModalDT.bind(this);
  }

  handleOpenModalDT () {
      this.setState({ showModalDT: true }); 
  }

  handleCloseModalDT () {
      this.setState({ showModalDT: false });
  }

  async componentWillMount() 
  {
    var appType = this.getParameterByName('page');     
    this.setState({appType: appType});
  }

  async componentDidMount() 
  {
    await this.getTreeListData();
    await this.getAllStartBlockData();
  }

  getParameterByName = (name, url = window.location.href) => {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

  //Start Block Function Section - Start//

  async addStartBlockData()
  {
    var apiData = await postStartBlockData(this.state.blockData);
    
    if(this.state.blockData.id < 1)
    {
      var updateBlockData = this.state.blockData;
      updateBlockData.id = apiData.SB_ID;

      this.setState({blockData: updateBlockData});
      apiData = await postStartBlockData(updateBlockData);
      console.log(this.state.blockData);
    }

    if(apiData)
    {
      this.getAllStartBlockData();
    }
  }

  async editStartBlockData(id)
  {
    var apiData = await getStartBlockDataAPI(id);

    if(apiData)
    {
      var sbJSON = JSON.parse(apiData.SBJSON.replace(/(\r\n|\n|\r)/gm, ""));
      sbJSON.id = id;

      if(sbJSON.decisionTree.trim() != "")
      {
        await this.getData(sbJSON.decisionTree);
      }

      this.setState({
        blockData: sbJSON
      });
    }
  }

  async deleteStartBlockData(id)
  {
    await deleteStartBlockDataAPI(id);
    await this.getAllStartBlockData();
  }
  
  async getAllStartBlockData()
  {
    var apiData = await getAllStartBlockAPI();
    if(apiData)
    {
      var apiArray = [];
      for(var element in apiData)
      {
        var itemJson = JSON.parse(apiData[element].SB_JSON);
        itemJson.id = apiData[element].SB_ID;
        itemJson.name = apiData[element].SB_Name;
        apiArray.push(itemJson);
      }

      this.setState({
        blockList: apiArray
      });
    }
  }
  
  async updateBlockData(block)
  {
    this.setState({blockData: block});
  }

  async updateDecisionTree(block)
  {
    if(block.decisionTree)
    {
      await this.getData(block.decisionTree);
    }

    this.updateBlockData(block);
  }
  
  //Start Block Function Section - End//





  //Decision Tree Function Section - Start//
  async previousPage()
  {
    await this.getTreeListData();
    this.setState({showTreePage: 'start'});
  }

   /* {
            subTitle:"",
            disable: false,
            nodeName: "",
            topTitle: "",
            titleLeft: "", 
            titleRight: "", 
            expanded: true, 
            type: 'text', 
            child: false, 
            nodeID: 0,
          },*/

  async createNewTree()
  {
    setTimeout(() => {
      this.setState({
        treeId: 0,
        treeName: '',
        subTitle: '',
        treeData: [
          {
            guid: this.createGuid(),
            nodeName: "",
            topTitle: "",
            title: "", 
            expanded: true, 
            type: 'question', 
            clearAfterAnswer: true,
            child: false, 
            nodeID: 0,
            children: [
              { 
                guid: this.createGuid(),
                nodeName: "",
                topTitle: "",
                title: "", 
                secondText: "",
                replaceButtonWText: false,
                expanded: true, 
                goToType: "termination",
                goToVal: 1,
                goToGuid: "",
                rightText: "",
                buttonAlign: "bottom", 
                type: 'answer', 
                child: true,
                children: [{
                  guid: this.createGuid(),
                  nodeName: "",
                  title: "", 
                  type: 'termination', 
                  child: true,
                  nodeType: "message",
                  academicVal: "",
                  academicOptions: [],
                  visitTypeVal: "",
                  visitTypeOptions: [],
                  epicVal: "",
                  epicOptions: [],
                  providerVal: "",
                  providerOptions: [],
                  expanded: true
                }]
              }
            ]}
          ],
        showTreePage: 'builder'
      });
    }, 1000);

    await this.getAllStartBlockData();
    await this.getDropDownListData();
  }

  async duplicateTree(treeId)
  {
    await this.getData(treeId);

    var treeName = this.state.treeName;

    for(var i = 0; i < this.state.treeList.length; i++)
    {
      var findTree = this.state.treeList.find(t => t.DT_Name.toLowerCase() == treeName.toLowerCase());
    
      if(findTree)
      {
        treeName = treeName + '(Copy)';
      }
      else
      {
        break;
      }
    }

    this.setState({
      treeId:0,
      treeName: treeName,
      showTreePage: 'builder'
    });
    
    await this.getAllStartBlockData();
    await this.getDropDownListData();
    await this.getTreeListData();
  }

  async editTree(treeId)
  {
    await this.getDropDownListData();

    await this.getData(treeId);
    
    var startBlock = await getStartBlockDataByTree(treeId);

    if(startBlock.SBJSON != null)
    {
      this.setState({
        blockData:  JSON.parse(startBlock.SBJSON.replace(/(\r\n|\n|\r)/gm, ""))
      })
    }

    this.setState({showTreePage: 'builder'});
    await this.getAllStartBlockData();
    await this.getTreeListData();
  }  

  async exportTree(treeId)
  {
    var apiData = await getDataAPI(treeId);
    
    if(apiData)
    {
      this.setState({exportTreeData: apiData});
    }         
  }  

  async deleteTree(treeId)
  {
    var apiData = await deleteDataAPI(treeId);

    if(apiData)
    {
      await this.getTreeListData();
      await this.getAllStartBlockData();
    }
  }

  async addUpdateData()
  {
    var apiData = await insertUpdateDataAPI(this.state.treeData, this.state.treeName, this.state.treeId);

    if(apiData.DT_ID > 0)
    {
      this.setState({
        treeId: apiData.DT_ID, 
        saveMessage: "Tree saved."
      }); 
      
      setTimeout(() => {this.setState({saveMessage: ""})}, 3000);
    }
    
    await this.getDropDownListData();
    await this.getTreeListData();
  }

  async getData(id)
  {
    var apiData = await getDataAPI(id);
    
    if(apiData.TreeName != null)
    { 
      var treeData = JSON.parse(apiData.TreeData.replace(/(\r\n|\n|\r)/gm, ""));

      treeData = this.cleanJSON(treeData);
      
      this.setState({
        treeId: id,
        treeName: apiData.TreeName,
        subTitle: (treeData[0].subTitle ? treeData[0].subTitle : ""),
        treeData: treeData,
        epicAPIKey: apiData.EpicAPI,
        epicEndpointUrl: apiData.EpicUrl
      });
    }
  }

  cleanJSON(jsonArray)
  {
    var newArray = jsonArray;

    /*Step 1 - Create GUIDs for each record if it doesn't have one*/
    for(var i = 0; i < newArray.length; i++)
    {
      if(!newArray[i].hasOwnProperty('guid'))
      {
        newArray[i].guid = this.createGuid();

        if(newArray[i].hasOwnProperty('children') && newArray[i].children.length > 0)
        {
          var children = newArray[i].children;

          for(var t = 0; t < children.length; t++)
          {
            if(!newArray[i].children[t].hasOwnProperty('guid'))
            {
              newArray[i].children[t].guid = this.createGuid();
            }

            if(newArray[i].children[t].hasOwnProperty('children') && newArray[i].children[t].children.length > 0)
            {
              var terminations = newArray[i].children[t].children;

              for(var l = 0; l < terminations.length; l++)
              {
                if(!newArray[i].children[t].children[l].hasOwnProperty('children'))
                {
                  newArray[i].children[t].children[l].guid = this.createGuid();
                }
              }
            }
          }
        }

      }
    }

    //Step 2 - If the node has a GoToValue on the question, convert that to a GoToGuid
    for(var i = 0; i < newArray.length; i++)
    {
      if(newArray[i].children && newArray[i].children.length > 0)
      {
        var children = newArray[i].children;

        for(var t = 0; t < children.length; t++)
        {
          if(newArray[i].children[t].goToVal)
          {
            if(parseInt(newArray[i].children[t].goToVal) > 0 && newArray[parseInt(newArray[i].children[t].goToVal)])
            {  
              newArray[i].children[t].goToGuid = newArray[parseInt(newArray[i].children[t].goToVal)].guid;
            }
            else
            {
              newArray[i].children[t].goToGuid = newArray[parseInt(newArray[i].nodeID)].guid;
            }
            
            delete newArray[i].children[t].goToVal;
          }
        }
      }      
    }

    //Step 3 - Remove the Text Node
    if(newArray[0].type == 'text')
    {
      var updatedArray = [];

      for(var i = 0; i < newArray.length; i++)
      {
        if(newArray[i].type != 'text')
        {
          updatedArray = [...updatedArray, newArray[i]];
        }
      }

      newArray = updatedArray;

      //Reorder the IDs in the nodes
      for(var i = 0; i < newArray.length; i++)
      {
        newArray[i].nodeID = i;
      }
    }

    /*Step 4 - Clean Providers, Departments and Visit Types*/
    /*Keeping this commented out until asked to bring it back*/
    /*for(var i = 0; i < newArray.length; i++)
    {
      if(newArray[i].hasOwnProperty('children') && newArray[i].children.length > 0)
      {
        var children = newArray[i].children;

        for(var t = 0; t < children.length; t++)
        {
          if(newArray[i].children[t].hasOwnProperty('children') && newArray[i].children[t].children.length > 0)
          {
            var terminations = newArray[i].children[t].children;

            for(var l = 0; l < terminations.length; l++)
            {
              if(newArray[i].children[t].children[l].visitTypeVal)
              {
                var visitTypeVal = newArray[i].children[t].children[l].visitTypeVal;

                var thisVisit = this.state.visitTypeList.find(r => r.VT_VisitTypeID == visitTypeVal);
                if(!thisVisit)
                {
                  newArray[i].children[t].children[l].visitTypeVal = "";
                }

              }

              if(newArray[i].children[t].children[l].epicOptions && newArray[i].children[t].children[l].epicOptions.length > 0)
              {
                var newEpics = [];
                var epicOptions = newArray[i].children[t].children[l].epicOptions;

                for(var j = 0; j < epicOptions.length; j++)
                {
                  var thisEpic = this.state.epicList.find(r => r.ED_Code == epicOptions[j].ED_Code);
                  if(thisEpic)
                  {
                    newEpics = [...newEpics, thisEpic];
                  }
                }

                newArray[i].children[t].children[l].epicOptions = newEpics;
              }

              if(newArray[i].children[t].children[l].providerOptions && newArray[i].children[t].children[l].providerOptions.length > 0)
              {
                
                var newProviders = [];
                var providerOptions = newArray[i].children[t].children[l].providerOptions;

                for(var j = 0; j < providerOptions.length; j++)
                {
                  var thisProvider = this.state.providerList.find(r => r.PL_NPI == providerOptions[j].PL_NPI);
                  if(thisProvider)
                  {
                    newProviders = [...newProviders, thisProvider];
                  }
                }

                newArray[i].children[t].children[l].providerOptions = newProviders;
              }
            }
          }
        }
      }
    }*/
    
    return newArray;
  }

  createGuid()
  {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
   });
  }
  

  async getTreeListData()
  {
    var apiData = await getAllTreeDataAPI();
    if(apiData)
    {
      this.setState({
        treeList: apiData
      });
    }
  }

  async getDropDownListData()
  {
    var allDropdownData = await getDropDownDataAPI();

    this.setState({
      providerList: allDropdownData.ResultsPL,
      academicList: allDropdownData.ResultsAD,
      epicList: allDropdownData.ResultsED,
      visitTypeList: allDropdownData.ResultsVT,
      epicUrl: allDropdownData.EpicUrl,
      epicAPI: allDropdownData.EpicAPI
    });
  }

  expandAndCollapse = (expanded) => {
    this.setState({
      treeData: toggleExpandedForAll({
        treeData: this.state.treeData,
        expanded,
      })
    });
  };

  updateTreeData(treeData) {
    this.setState({ treeData });
  }

  handleText = (textNode, textPath, textNodeKey) => {
    this.setState(state => ({
        treeData: changeNodeAtPath({
            treeData: state.treeData,
            path: textPath,
            newNode: textNode,
            getNodeKey: textNodeKey
        })
    }));
}

  handleQuestion = (questionNode, questionPath, questionNodeKey) => {
      this.setState(state => ({
          treeData: changeNodeAtPath({
              treeData: state.treeData,
              path: questionPath,
              newNode: questionNode,
              getNodeKey: questionNodeKey
          })
      })); 
  }

  jumpToTree = (id) => {

    this.state.treeList.filter(
     (data) =>{ 
      if(data.DT_ID == id){
        this.setState({ treeData: data.DT_JSON });
      }}
    );

  }
  
  handleAnswer = (answerNode, answerPath, answerNodeKey) => {
    this.setState(state => ({
      treeData: changeNodeAtPath({
          treeData: state.treeData,
          path: answerPath,
          newNode: answerNode,
          getNodeKey: answerNodeKey
        })
    }));
  }


  handleTermination = (terminationNode, terminationPath, terminationNodeKey) => {
    this.setState(state => ({
      treeData: changeNodeAtPath({
          treeData: state.treeData,
          path: terminationPath,
          newNode: terminationNode,
          getNodeKey: terminationNodeKey
        })
    }));
  }

  removeNode = (path) => {
      this.setState(state => ({
        treeData: removeNodeAtPath({
          treeData: state.treeData,
          path,
          getNodeKey: ({ treeIndex }) => treeIndex,
        })
      }));
  }

  selectThis = (node, path) => {
    this.setState({ currentNode: node, path: path });
  }

  updateSubTitle = (value) => {    
    var updatedJson = this.state.treeData;
    updatedJson[0].subTitle = value;

    this.setState({ 
      treeData: updatedJson,
      subTitle: value
     });
  }

  insertNewNode = (type, nodePath) => {  
    /*if(type === "text")
    {
      var questionCount = this.state.treeData.length;

      this.setState(state => ({
        treeData: insertNode({
          treeData: state.treeData,
          depth: 0,
          newNode: { 
            disable: false,
            nodeName: "",
            topTitle: "",
            titleLeft: "", 
            titleRight: "", 
            expanded: true, 
            type: 'text', 
            child: false, 
            nodeID: (questionCount),
          },
          getNodeKey:({ treeIndex }) => treeIndex
        }).treeData
      }));
    }*/

    if(type === "question")
    {
      var questionCount = this.state.treeData.length;

      this.setState(state => ({
        treeData: insertNode({
          treeData: state.treeData,
          depth: 0,
          newNode: { 
            guid: this.createGuid(),
            nodeName: "",
            topTitle: "",
            title: "", 
            expanded: true, 
            type: 'question', 
            clearAfterAnswer: true,
            child: false, 
            nodeID: (questionCount),
            children: []
          },
          getNodeKey:({ treeIndex }) => treeIndex
        }).treeData
      }));
    }

    if(type === "answer")
    {
      this.setState(state => ({
        treeData: addNodeUnderParent({
          treeData: state.treeData,
          parentKey: nodePath[nodePath.length - 1],
          expandParent: true,
          depth: 1,
          newNode: { 
            guid: this.createGuid(),
            nodeName: "",
            topTitle: "",
            title: "", 
            secondText: "",
            replaceButtonWText: false,
            goToType: 'question',
            goToVal: 1,
            goToGuid: "",
            expanded: true, 
            rightText: "",
            buttonAlign: "bottom", 
            type: 'answer', 
            child: true, 
            hasTerm: false,
            children: []
           },
            getNodeKey: ({ treeIndex }) => treeIndex
        }).treeData
      }));
    }

    if(type === "termination")
    {
      this.setState(state => ({
        numTermination: this.state.numTermination + 1,
        treeData: addNodeUnderParent({
          expandParent: true,
          parentKey: nodePath[nodePath.length - 1],
          treeData: state.treeData,
          depth: 2,
          canNodeHaveChildren: false,
          newNode: { 
            guid: this.createGuid(),
            nodeName: "",
            title: "", 
            expanded: true, 
            type: 'termination', 
            child: true, 
            nodeType:"message",
            academicVal: "",
            academicOptions: [],
            visitTypeVal: "",
            visitTypeOptions: [],
            epicVal: "",
            epicOptions: [],
            providerVal: "",
            providerOptions: [],},
          getNodeKey: ({ treeIndex }) => treeIndex
        }).treeData
      }));
    }
  }

  //Decision Tree Function Section - End//
  
  render() {
    const { 
      showTreePage,
      treeId,
      treeName,
      treeData, 
      treeList, 
      subTitle,
      searchFocusIndex, 
      searchString,
      saveMessage,
      failMessage,
      questionList,
      appType,
      blockData,
      blockList,
      epicList,
      epicAPIKey,
      epicEndpointUrl,
      showModalDT,
      exportTreeData,
      exportBlockData
    } = this.state;

    
    const getNodeKey = ({ treeIndex }) => treeIndex;
    const count = getVisibleNodeCount({treeData:this.state.treeData});

    const treeHeaders = [
      { label: 'TreeName', key: 'TreeName' },
      { label: 'TreeData', key: 'TreeData' },
      { label: 'EpicUrl', key: 'Epic Url' },
    ];

    const editorSettings = {
      content_css: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
      height: 200,
      menubar: false,
      force_br_newlines : true,
      force_p_newlines : false,
      forced_root_block : '',
      cleanup : false,
      verify_html : false,
      relative_urls : false,
      remove_script_host : false,
      noneditable_noneditable_class: 'fas',
      extended_valid_elements: 'i[*]',
      fontsize_formats: "10px 12px 14px 16px 18px 24px 36px",
      placeholder: 'Enter Content',
      plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code wordcount',
          'fontawesome noneditable'
      ],
      toolbar: 'undo redo | formatselect | fontsizeselect |' +
      'bold italic backcolor | alignleft aligncenter ' +
      'alignright alignjustify | bullist numlist outdent indent | ' +
      'removeformat | link image | code | fontawesome',
      content_style: 'body { font-family:"Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif; font-size:16px; } .fas {font-size: 22px;}'
  };

    var builderHeight = this.state.treeData.length * 2000;

    return (
      <React.Fragment>
          <ReactModal 
              isOpen={showModalDT}
              contentLabel="Decision Tree Preview"
              className="Modal"
              preventScroll={false}
            >
              <DTPreview
                treeName={treeName}
                treeData={treeData} 
                blockData={blockData}
                epicList={epicList}
                epicUrl={epicEndpointUrl}
                epicAPI={epicAPIKey}
                />
              <button className="btn btn-secondary close-modal" onClick={this.handleCloseModalDT}>Close Modal</button>
            </ReactModal> 

      {appType == "decision-tree-builder" && showTreePage == 'start' && 
        <React.Fragment>

          <button className="btn btn-primary" onClick={() => this.createNewTree()}>Create New Decision Tree</button>
          <div className="row">
            <div className="col-sm-12">
              {exportTreeData.length > 0 && 
                <CSVLink data={exportTreeData} headers={treeHeaders}>
                  Download Tree
                </CSVLink>
              }
            </div>
          </div>
          
          <DTTable 
            treeList={treeList}
            blockList = {blockList}
            duplicateTree={(values) => this.duplicateTree(values.id)}
            editTree={(values) => this.editTree(values.id)}
            exportTree={(values) => this.exportTree(values.id)}
            deleteTree={(values) => {if (window.confirm('Are you sure you wish to delete this item?')) this.deleteTree(values.id)}}
          />      
        </React.Fragment>
      }
      {appType == "decision-tree-builder" && showTreePage == 'builder' &&
        <React.Fragment>
          <div className="container decision-tree-container" style={{ height: builderHeight + 200 }}>
              <div className="row margin-bottom-25">
                <label htmlFor="name" className="col-sm-2 col-form-label">Decision Tree Name:</label>
                <div className="col-sm-4" style={{ width: '300px' }}>            
                  <input className="form-control" type="text" name="name" onChange={(e) => this.setState({ treeName: e.target.value })} value={treeName} style={{ marginRight: '10px' }}/>       
                  {saveMessage != "" && <div className="alert alert-success">{saveMessage}</div>}
                  {failMessage != "" && <div className="alert alert-danger">{failMessage}</div>}
                </div>

                <div className="col-sm-4" style={{width:"50%"}}>
                  {treeName && treeName.trim() != "" && 
                    <button className="btn btn-primary margin-right-25" onClick={(e) => {e.preventDefault(); this.addUpdateData();}}>Save Tree</button>
                  }
                  {treeId > 0 && 
                    <button className="btn btn-danger margin-right-25" onClick={(e) => {e.preventDefault(); this.deleteData();}}>Delete Tree</button>
                  }
                    <button className="btn btn-danger" onClick={(e) => {e.preventDefault(); this.previousPage();}}>Previous Page</button>
                </div>  

              </div>

              {treeId > 0 && 
                <React.Fragment>
                  <div className="row margin-bottom-25">
                    <div className="stay" style={{width:"120px"}}>
                      <p>
                        <button className="btn btn-primary form-control" onClick={(e) => { e.preventDefault(); this.insertNewNode('question', getNodeKey); }}>Add Question</button>
                      </p>
                      <p>
                        <button className="btn btn-primary form-control" onClick={(e) => { e.preventDefault(); e.stopPropagation(); this.handleOpenModalDT();}}>Preview Tree</button>
                      </p>
                      <p>
                        <button className="btn btn-secondary form-control" onClick={() => { this.expandAndCollapse(true); }}>Expand all</button>
                      </p>
                      <p>
                        <button className="btn btn-secondary form-control" onClick={() => { this.expandAndCollapse(false); }}>Collapse all</button>
                      </p>
                      <p>
                        <button className="btn btn-primary form-control" onClick={(e) => {e.preventDefault(); this.addUpdateData();}}>Save Tree</button>
                        {saveMessage != "" && <React.Fragment><div className="alert alert-success">Tree Saved</div></React.Fragment>}
                        {failMessage != "" && <div className="alert alert-danger">{failMessage}</div>}
                      </p>
                    </div>
                  </div>

                  <div className="row margin-bottom-25">
                    <label htmlFor="name" className="col-sm-2 col-form-label">Subtitle Instructions:</label>
                    <div className="col-sm-4" style={{ width: '300px' }}>            
                        <input className="form-control" placeholder="Enter subtitle instructions" type="text" name="name" onChange={(e) => this.updateSubTitle(e.target.value)}  value={subTitle}/>       
                    </div>
                  </div>  
                  <div className="row">

                    <div className="col-sm-12">
                    
                      <div className="sortable-tree-container" style={{ display: 'block' , height: builderHeight, width: "100%"}}>
                      
                        <SortableTree
                          searchQuery={searchString}
                          onChange={this.updateTreeData}
                          searchFocusOffset={searchFocusIndex}
                          treeData={treeData}
                          canDrag={({node}) => (node.type === "question" || node.type === "answer")}
                          canDrop={({node, nextParent,prevParent}) => (prevParent.type == nextParent.type || node.children[0].type == "answer" )}
                          onChange={treeData => this.setState({treeData})}
                          generateNodeProps={({node, path, treeIndex}) => ({
                            title: (
                              <form >
                                {node.type == 'text' && 
                                  <TextNode 
                                    node={node} 
                                    path={path}
                                    treeData={treeData}
                                    questionList={questionList}
                                    getNodeKey={getNodeKey}
                                    newNode={(values) => this.insertNewNode(values.type, values.nodePath)}
                                    textUpdate={(values) => this.handleText(values.textNode, values.textPath, values.textNodeKey)}
                                    deleteNode={(path) => this.removeNode(path)}
                                    editorSettings={editorSettings}
                                  />
                                }

                                {node.type == 'question' && 
                                  <QuestionNode 
                                    node={node} 
                                    path={path}
                                    treeData={treeData}
                                    questionList={questionList}
                                    getNodeKey={getNodeKey}
                                    newNode={(values) => this.insertNewNode(values.type, values.nodePath)}
                                    questionUpdate={(values) => this.handleQuestion(values.questionNode, values.questionPath, values.questionNodeKey)}
                                    deleteNode={(path) => this.removeNode(path)}
                                    editorSettings={editorSettings}
                                  />
                                }

                                {node.type == 'answer' && 
                                  <AnswerNode 
                                    node={node} 
                                    path={path} 
                                    treeData={treeData}
                                    treeList={treeList}
                                    getNodeKey={getNodeKey}
                                    newNode={(values) => this.insertNewNode(values.type, values.nodePath)}
                                    answerUpdate={(values) => this.handleAnswer(values.answerNode, values.answerPath, values.answerNodeKey )}
                                    deleteNode={(values) => this.removeNode(path)}
                                    editorSettings={editorSettings}
                                  />
                                }

                                { node.type == 'termination' && 
                                  <TerminateNode 
                                    node={node} 
                                    path={path}
                                    academicList= {this.state.academicList}
                                    visitTypeList = {this.state.visitTypeList}
                                    epicList = {this.state.epicList}
                                    providerList= {this.state.providerList}
                                    deleteNode={(values) => this.removeNode(path)}
                                    terminationUpdate={(values) => this.handleTermination(values.terminationNode, values.terminationPath, values.terminationNodeKey )}
                                    getNodeKey={getNodeKey} 
                                    editorSettings={editorSettings}
                                  />
                                }
                                
                              </form>
                            ),
                          })}
                        />
                        
                      </div>

                    </div>
                  

                  </div>
              </React.Fragment>
              }
          </div>
        </React.Fragment>
    }

    {appType == "manage-start-blocks" &&
    <div className="container">
      <StartBlockForm 
        treeList={treeList}
        blockData={blockData}
        blockList = {blockList}
        decisionTreeUpdate={(block) => this.updateDecisionTree(block)}
        blockUpdate={(block) => this.updateBlockData(block)}
        previewStartBlock={() => this.handleOpenModalDT()}
        addStartBlockData={() => this.addStartBlockData()}
        editorSettings={editorSettings}
      />
      <StartBlockTable 
        treeList={treeList}
        blockList = {blockList}
        editStartBlock={(values) => this.editStartBlockData(values.id)}
        deleteStartBlock={(values) => {if (window.confirm('Are you sure you wish to delete this item?')) this.deleteStartBlockData(values.id)}}
      />      
    </div>

    }
    </React.Fragment>
    );
  }
}
export default App;
