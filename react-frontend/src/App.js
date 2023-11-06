import './App.css';
import React, { Component } from "react";
import {
  getDataAPI,
  getStartBlockDataByTree,
  getDropDownDataAPI,
  saveUserJourney,
  pageUrl
} from './services/apiCalls';
import ReactHtmlParser from 'react-html-parser'; 

class App extends Component {
  constructor(state) {
    super(state);

    this.state = {    
      sessionId: '',
      questionTrail: [],
      userJourney: [],
      showStartBlock: false,
      appType: "",
      showModalDT: false,
      showModalSB: false,
      searchString: '',
      treeName: '',
      treeId:0,
      epicUrl: '',
      epicAPI: '',
      searchFocusIndex: 0,
      currentNode: {},
      treeList:[],
      treeData: [],
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
      blockData: {},
      blockList: [],
      showRestart: false,
      finalize: '',
      finalizeType: null,
    }      
  }
  async componentWillMount() 
  {
    var uid = Date.now().toString(36) + Math.random().toString(36);
    this.setState({sessionId: uid});
    saveUserJourney(uid);

    var treeElement = document.querySelector("#frontendApp");
    var treeId = treeElement.getAttribute('data-id');
    
    await this.getData(treeId);
    var startBlock = await getStartBlockDataByTree(treeId);

    var jsonSB = null; 

    if(startBlock.SBJSON != null)
    {
      saveUserJourney(this.state.sessionId, startBlock.SBID, "Load Start Block - " + startBlock.SBName);

      jsonSB = JSON.parse(startBlock.SBJSON);
      jsonSB.id = parseInt(startBlock.SBID);

      this.setState({
        blockData: jsonSB
      });
    }

    if(this.state.blockData.id > 0)
    {
      this.setState({showStartBlock: true});
    }
    else
    {
      this.startQuestions();
    }

    var allDropdownData = await getDropDownDataAPI();

    this.setState({
      providerList: allDropdownData.ResultsPL,
      academicList: allDropdownData.ResultsAD,
      epicList: allDropdownData.ResultsED,
      visitTypeList: allDropdownData.ResultsVT
    });
  }

  componentDidMount() 
  {
 
  }

  async getData(id)
  {
    var apiData = await getDataAPI(id);
    
    if(apiData.TreeName)
    {
      this.setState({
        treeId: id,
        treeName: apiData.TreeName,
        treeData: JSON.parse(apiData.TreeData.replace(/(\r\n|\n|\r)/gm, "")),
        epicUrl: apiData.EpicUrl,
        epicAPI: apiData.EpicAPI,
      });

      saveUserJourney(this.state.sessionId, null, null, id, "Load Tree - " + apiData.TreeName);
    }
  }


  startBlockTreeButton()
  {
    var sbBlock = document.querySelector('#sb-block');
    sbBlock.classList.add('hide');
    this.startQuestions();
  }

  startBlockTreeButton()
  {
    var sbBlock = document.querySelector('#sb-block');
    sbBlock.classList.add('hide');

    saveUserJourney(this.state.sessionId, null, null, this.state.treeId, "Start Tree - " +  this.state.treeName);
    
    this.startQuestions();
  }

  startQuestions()
  {
    if(this.state.treeData)
    {
      var initList = [];

      if(this.state.treeData[0].type == 'text')
      {
        initList = [this.state.treeData[1]];
      }
      else
      {
        initList = [this.state.treeData[0]];
      }
      
      saveUserJourney(this.state.sessionId, null, null, this.state.treeId, this.state.treeName, initList[0].title);

      this.setState({questionTrail: initList});

      var allTerms = document.querySelectorAll('.termination');
      for(var i = 0; i < allTerms.length; i++)
      {
        allTerms[i].classList.add('hide-termination');
      }
    }
  }
  
  answerQuestion(question, answer, answerNode, key)
  {
    this.setState({showRestart: true});

    //var findQuestion = this.state.treeData[question]; 
    var findQuestion = this.state.treeData.find(t => t.nodeID == question); 
    if(!findQuestion)
    {
      findQuestion = this.state.treeData[question]; 
    }

    switch(answerNode.goToType)
    {
      case 'termination': 

        var termMessage = document.querySelector('#term-'+key+'-'+answer+'-0');

        if(termMessage)
        {
          termMessage.classList.remove('hide-termination');
          
          this.setState({
            userJourney: [...this.state.userJourney, {questionAnswered: question, selectedAnswer: answer, goToType: answerNode.goToType, goToVal: answerNode.goToVal}]
          });

          if(answerNode.children.length > 0)
          {
            saveUserJourney(this.state.sessionId, null, null, this.state.treeId, this.state.treeName, findQuestion.title, answerNode.title, answerNode.children[0].nodeType);          
          }
        }

        if(answerNode.child && answerNode.children[0].nodeType == 'epic')
        {
          this.setState({
            finalizeType: 'epic',
            finalize: 'term-'+key+'-'+answer+'-0'
          });           
        }
        break;
      case 'question':
        //var nextQuestion = this.state.treeData[answerNode.goToVal];

        var nextQuestion = null;
        if(answerNode.goToGuid)
        {
          nextQuestion = this.state.treeData.find(q => q.guid == answerNode.goToGuid);
        }
        else if (answerNode.goToVal)
        {
          nextQuestion = this.state.treeData[answerNode.goToVal];
        }
        
        this.setState({
          questionTrail: [...this.state.questionTrail, nextQuestion],
          userJourney: [...this.state.userJourney, {questionAnswered: question, selectedAnswer: answer, goToType: answerNode.goToType, goToVal: answerNode.goToVal}]
        });

        saveUserJourney(this.state.sessionId, null, null, this.state.treeId, this.state.treeName, findQuestion.title, answerNode.title);

        break;
    }

    var answerButton = document.querySelector('#answer-'+key+'-'+answer);
    answerButton.classList.remove('btn-primary');
    answerButton.classList.add('btn-danger');

    var allButtons = document.querySelectorAll('[id^=answer-'+key+']');
    for(var i = 0; i < allButtons.length; i++)
    {
      allButtons[i].disabled = true;
    }
    
    if(findQuestion.clearAfterAnswer)
    {
      var topPage = document.querySelector('#content');
      var topPage2 = document.querySelector('#main');
      
      if(!topPage && topPage2)
      {
        topPage = topPage2;
      }
      else if(!topPage && !topPage2)
      {
        topPage = document.querySelector('#top-of-tree');
      }
      
      //topPage.scrollIntoView({ behavior: 'smooth', block: 'start' });

      var fullQuestions = document.querySelectorAll('.fullQuestion');
      for(var i = 0; i < fullQuestions.length; i++)
      {
        fullQuestions[i].classList.add('hide');
      }

      var fullTexts = document.querySelectorAll('.fullText');
      for(var i = 0; i < fullTexts.length; i++)
      {
        fullTexts[i].classList.add('hide');
      }
    }
  }

  resetTree()
  {
    this.setState({showRestart: false});

    saveUserJourney(this.state.sessionId, (this.state.blockData && this.state.blockData.id > 0 ? this.state.blockData.id : null), (this.state.blockData && this.state.blockData.id ? "Restart Block - " + this.state.blockData.name : null), this.state.treeId, "Restart Tree - " +  this.state.treeName);

    var sbBlock = document.querySelector('#sb-block');
    if(sbBlock)
    {
      sbBlock.classList.remove('hide');
    }

    var fullQuestions = document.querySelectorAll('.fullQuestion');
    for(var i = 0; i < fullQuestions.length; i++)
    {
      fullQuestions[i].classList.remove('hide');
    }

    var fullTexts = document.querySelectorAll('.fullText');
    for(var i = 0; i < fullTexts.length; i++)
    {
      fullTexts[i].classList.remove('hide');
    }

    var allButtons = document.querySelectorAll('[id^=answer-]');
    for(var i = 0; i < allButtons.length; i++)
    {        
      allButtons[i].disabled = false;
      allButtons[i].classList.remove('btn-danger');
      allButtons[i].classList.add('btn-primary');
    }

    this.setState({
      questionTrail: [],
      finalize: false,
      finalizeType: null
    });

    if(this.state.blockData.id > 0)
    {
      this.setState({showStartBlock: true});
    }
    else
    {
      this.startQuestions();
    }
  }

  showEpic(currentTermination ,finalize, finalizeType, termValues)
  {
    if(finalize !== '' && currentTermination == finalize && finalizeType == 'epic')
    {
      var addParams = {};

      var deptList = "";
      if(termValues.epicOptions.length > 0)
      {
        for(var i = 0; i < termValues.epicOptions.length; i++)
        {
          var value = termValues.epicOptions[i].ED_Code.trim();
          deptList += value + ',';
        }

        if(deptList.length > 0)
        {
          deptList = deptList.substring(0, deptList.length - 1);

          addParams = {...addParams, dept: deptList}
        }
      }

      var providerList = "";
      if(termValues.providerOptions.length > 0)
      {
        for(var i = 0; i < termValues.providerOptions.length; i++)
        {
          var value = termValues.providerOptions[i].PL_NPI.trim();
          providerList += value + ',';
        }

        if(providerList.length > 0)
        {
          providerList = providerList.slice(0,-1);

          addParams = {...addParams, id: providerList}
        }
      }

      if(deptList == "" && providerList == "")
      {
        var academnicId = termValues.academicVal;
        var epicDepts = this.state.epicList.filter(el => el.AD_Code == academnicId);

        var defaultDeptList = "";
        for(var i = 0; i < epicDepts.length; i++)
        {
          var value = epicDepts[i].ED_Code.trim();
          defaultDeptList += value + ',';
        }

        if(defaultDeptList.length > 0)
        {
          defaultDeptList = defaultDeptList.slice(0,-1);

          addParams = {...addParams, dept: defaultDeptList}
        }
      }

      var visitType = "";
      if(termValues.visitTypeVal.length > 0)
      {
        visitType = termValues.visitTypeVal.trim();

        addParams = {...addParams, vt: visitType}
      }
      
      return(
        <React.Fragment>
          {
            window.mychartWidget.renderWidget({
              "url": this.state.epicUrl,
              "apiKey": this.state.epicAPI,
              "widgetType": "openscheduling",
              "containerId":"scheduleContainer",                                                
              "additionalParams": addParams 
              })
          }

          <div id="scheduleContainer"></div>
        </React.Fragment>
      );
    }
  }

  render() 
  {
    const { 
      treeData,
      treeName,
      subTitle,
      blockData,
      questionTrail,
      showStartBlock,
      showRestart,
      finalize, 
      finalizeType
    } = this.state;

    var showStartBlockRight = (blockData.rightContentTop && blockData.rightBtnName && blockData.rightContentBottom);
    var colFormat = blockData.colFormat;

    return (
      <div className={`decision-tree-content ${blockData.colFormat === 'individual' ? 'profile' : ''}`}>
        <div className="" id="top-of-tree">
        {treeData &&
          <React.Fragment>
            {/*<h2 className="page-title">{treeName--commented out}</h2>
            <h3 className="treeSubHeader">{subTitle--commented out}</h3>*/}
            {showRestart && 
              <p>
                <a className="pointer dt-link" onClick={() => this.resetTree()}>Start Over</a> 
              </p>
            }
          </React.Fragment>
        }

        {blockData && showStartBlock && (colFormat == '2Column') &&
          <div className="sb-preview" id="sb-block">
              <div className="sbTitle">
                <h2>{blockData.title}</h2>
              </div>
              <div className="sb-preview-cols">
                <div className={showStartBlockRight ? "sb-preview-col border-middle" : "sb-preview-full"}>
                    <div className="sb-content">{ReactHtmlParser(blockData.leftContentTop)}</div>
                    <button className="btn btn-danger btn-round" onClick={() => this.startBlockTreeButton()}>{blockData.leftBtnName}</button>
                    <div className="sb-content">{ReactHtmlParser(blockData.leftContentBottom)}</div>
                </div>
                {showStartBlockRight &&
                  <div className="sb-preview-col">
                      <div className="sb-content">{ReactHtmlParser(blockData.rightContentTop)}</div>
                      <div class="btn-wrapper">
                        <button className="btn btn-danger btn-round" href={blockData.rightBtnLink}>{blockData.rightBtnName}</button>
                      </div>
                      <div className="sb-content">{ReactHtmlParser(blockData.rightContentBottom)}</div>
                  </div>
                }
              </div>
              <div className="sb-bottom-center">{ReactHtmlParser(blockData.centerBottom)}</div>
            </div>             
        }

        {blockData && showStartBlock && (colFormat == '1Column') &&
          <div className="sb-preview" id="sb-block">
            {(blockData.title) && 
              <div className="sbTitle">
                <h2>{blockData.title}</h2>
              </div>
            }
            <div className="sb-preview-cols">
              <div className="sb-preview-full">
                {(blockData.leftContentTop) && 
                  <div className="sb-content">{ReactHtmlParser(blockData.leftContentTop)}</div>
                }
                {(blockData.leftBtnName) && 
                    <button className="btn btn-danger btn-round" onClick={() => this.startBlockTreeButton()}>{blockData.leftBtnName}</button>
                }
                {(blockData.leftContentBottom) &&
                  <div className="sb-content">{ReactHtmlParser(blockData.leftContentBottom)}</div>
                }
               </div>
            </div>
            {(blockData.centerBottom) &&
              <div className="sb-bottom-center">{ReactHtmlParser(blockData.centerBottom)}</div>
            }
          </div>              
        }

        {blockData && showStartBlock && (colFormat === 'individual') &&
          <div className="sb-preview-ind" id="sb-block">
            <div className={"sb-preview-full"}>
              <div className='right'>
                {(blockData.title) && <h2 className='title'>{blockData.title}</h2>}
                  <div>
                    {(blockData.leftContentTop) &&
                      <div className="sb-content">{ReactHtmlParser(blockData.leftContentTop)}</div>
                    }
                    {(blockData.leftBtnName) && (blockData.centerBottom) &&
                      <div class="ind-btn-wrapper">
                        <button className="btn btn-danger btn-round" onClick={() => this.startBlockTreeButton()}>{blockData.leftBtnName}</button>
                        <div className="bottom center">{ReactHtmlParser(blockData.centerBottom)}</div>
                      </div>
                    }
                  </div>
  
              </div>
            </div>  
          </div>                  
        }

          {treeData &&
            <React.Fragment>
            {questionTrail && questionTrail.length > 0 && questionTrail.map((value, key) => {
              return(
                <React.Fragment>
                    {value.type == 'question' &&
                      <React.Fragment>
                      <div className={"fullQuestion " + (value.topTitle.trim() != "" ? "top-border" : "")}>                      
                        {value.topTitle.trim() != "" && 
                          <div className="topTitle sb-content">
                            {value.topTitle}
                          </div>
                        }
                        <div className={value.topTitle.trim() == "" ? "border" : ""}>
                          <div className="question preview-question  margin-top-25 margin-right-25 margin-bottom-25 margin-left-25 sb-content" id={"question-" + key}>{ReactHtmlParser(value.title)}</div>
                            {value.children.length > 0 && value.children.map((answerValue, answerKey) => {
                            return(
                              <React.Fragment key={answerKey}>
                                {answerValue.topTitle.trim() != "" && 
                                  <div className="topTitle sb-content">
                                    {answerValue.topTitle}
                                  </div>
                                }
                                {answerValue.buttonAlign == 'bottom' &&
                                  <button className='dt-button answer margin-bottom-25 margin-left-25 margin-right-25' id={'answer-'+key+'-'+answerKey} onClick={() => this.answerQuestion(value.nodeID, answerKey, answerValue, key)}>{answerValue.title.replace(/(<([^>]+)>)/gi, "")}</button>
                                }

                                {answerValue.buttonAlign == 'right' &&
                                  <React.Fragment>
                                    <div className="answerTextLeft answerLeft sb-content">
                                      {ReactHtmlParser(answerValue.rightText)}
                                    </div>
                                    {answerValue.replaceButtonWText && 
                                      <div className="secondText">
                                        {ReactHtmlParser(answerValue.secondText)}
                                      </div>
                                    }
                                    {!answerValue.replaceButtonWText && 
                                      <button className='dt-button answer answerLeft' id={'answer-'+key+'-'+answerKey} onClick={() => this.answerQuestion(value.nodeID, answerKey, answerValue, key)}>{answerValue.title.replace(/(<([^>]+)>)/gi, "")}</button>
                                    }
                                    
                                  </React.Fragment>
                                }
                                
                              </React.Fragment>
                              )
                            })
                          }
                        </div>
                      </div>

                        {value.children.length && value.children.length > 0 && value.children.map((answerValue, answerKey) => {
                          return(
                            <React.Fragment key={answerKey}>
                            {answerValue.children && answerValue.children.length > 0 && answerValue.children.map((termValue, termKey) => {
                                return(
                                  <React.Fragment key={termKey}>
                                    {termValue.nodeType == 'message' && 
                                      <React.Fragment>
                                        <div className='termination termination-style hide-termination sb-content' id={'term-'+key+'-'+answerKey+'-'+termKey}>{ReactHtmlParser(termValue.title)}</div>
                                      </React.Fragment>
                                    }

                                    {termValue.nodeType == 'epic' && 
                                      <React.Fragment>
                                        <div id={'term-'+key+'-'+answerKey+'-'+termKey} className='termination hide-termination'>
                                          {this.showEpic('term-'+key+'-'+answerKey+'-'+termKey, finalize, finalizeType, termValue)}
                                        </div>
                                      </React.Fragment>
                                    }
                                    
                                  </React.Fragment>
                                )
                              }
                            )}
                            </React.Fragment>
                          )

                          })
                        
                        }
                    </React.Fragment>
                    }
                </React.Fragment>
              )
            })}
          </React.Fragment>
          }
        </div>
      </div>
      );
    }
}
export default App;
