import React from 'react';
import ReactHtmlParser from 'react-html-parser'; 
//import ScriptTag from "react-script-tag";
//import {Helmet} from 'react-helmet';

class DTPreview extends React.Component {
    constructor(props) {
      super(props);
  
      this.state = {    
        questionTrail: [],
        userJourney: [],
        showStartBlock: false,
        finalize: '',
        finalizeType: null,
      }
      
    }

    async componentDidMount() 
    {

      if(this.props.blockData.id > 0)
      {
        this.setState({showStartBlock: true});
      }
      else
      {
        this.startQuestions();
      }
    }

    startBlockTreeButton()
    {
      var sbBlock = document.querySelector('#sb-block');
      sbBlock.classList.add('hide');
      this.startQuestions();
    }

    startQuestions()
    {
      if(this.props.treeData)
      {
        var initList = [];
        if(this.props.treeData[0].type == 'text')
        {
          initList = [this.props.treeData[1]];
        }
        else
        {
          initList = [this.props.treeData[0]];
        }

        this.setState({questionTrail: initList});

        var allTerms = document.querySelectorAll('.termination');
        for(var i = 0; i < allTerms.length; i++)
        {
          allTerms[i].classList.add('hide-termination');
        }
      }
    }

    newTree = (id) => {
        this.props.jumpToTree(id);
    }
    
    answerQuestion(question, answer, answerNode, key)
    {
      //var findQuestion = this.props.treeData[question]; 
      var findQuestion = this.props.treeData.find(t => t.nodeID == question); 
      if(!findQuestion)
      {
        findQuestion = this.props.treeData[question]; 
      }

      switch(answerNode.goToType)
      {
        case 'termination': 

          var termMessage = document.querySelector('#term-'+key+'-'+answer+'-0');

          if(termMessage)
          {
            termMessage.classList.remove('hide-termination');
            this.setState({userJourney: [...this.state.userJourney, {questionAnswered: question, selectedAnswer: answer, goToType: answerNode.goToType, goToVal: answerNode.goToVal}]});
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

          var nextQuestion = null;
          if(answerNode.hasOwnProperty('goToGuid'))
          {
            nextQuestion = this.props.treeData.find(q => q.guid == answerNode.goToGuid);
          }
          else if (answerNode.hasOwnProperty('goToVal'))
          {
            nextQuestion = this.props.treeData[answerNode.goToVal];
          }
          
          this.setState({
            questionTrail: [...this.state.questionTrail, nextQuestion],
            userJourney: [...this.state.userJourney, {questionAnswered: question, selectedAnswer: answer, goToType: answerNode.goToType, goToVal: answerNode.goToVal}]
          });
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

      if(this.props.blockData.id > 0)
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
          var epicDepts = this.props.epicList.filter(el => el.AD_Code == academnicId);

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
                "url": this.props.epicUrl,
                "apiKey": this.props.epicAPI,
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

    render() {

      const { 
        treeData,
        treeName,
        subTitle,
        blockData,
      } = this.props;

      const {
        questionTrail,
        showStartBlock,
        finalize, 
        finalizeType
      } = this.state;

      var colFormat = (blockData.colFormat); 
      var showStartBlockRight = (blockData.rightContentTop && blockData.rightBtnName && blockData.rightContentBottom && (colFormat == '2Column'));
      

      return (
        <React.Fragment>
          <div className="page-content">
          
          {treeData &&
            <React.Fragment>
              <h2 className="treeHeader">{treeName}</h2>
              <h3 className="treeSubHeader">{subTitle}</h3>
              <p>
                <button className="btn btn-link" onClick={() => this.resetTree()}>Start Over</button> 
              </p>
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
                        <div class="link-btn-container">
                          <a className="btn btn-danger btn-round" target="_blank" href={blockData.rightBtnLink}>{blockData.rightBtnName}</a>
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
                <div className="sbTitle">
                  <h2>{blockData.title}</h2>
                </div>
                <div className="sb-preview-cols">
                  <div className={showStartBlockRight ? "sb-preview-col border-middle" : "sb-preview-full"}>
                      <div className="sb-content">{ReactHtmlParser(blockData.leftContentTop)}</div>
                      <button className="btn btn-danger btn-round" onClick={() => this.startBlockTreeButton()}>{blockData.leftBtnName}</button>
                      <div className="sb-content">{ReactHtmlParser(blockData.leftContentBottom)}</div>
                  </div>
                </div>
                <div className="sb-bottom-center">{ReactHtmlParser(blockData.centerBottom)}</div>
              </div>             
          }

        {blockData && showStartBlock && (colFormat === 'individual') &&
          <div className="sb-preview-ind" id="sb-block">
            <div className={"sb-preview-full"}>
              <div className='right'>
                <h2 className='title'>{blockData.title}</h2>

                  <div>
                    <div className="sb-content">{ReactHtmlParser(blockData.leftContentTop)}</div>
                    <div class="ind-btn-wrapper">
                      <div class="btn-container">
                        <button className="btn btn-danger btn-round" onClick={() => this.startBlockTreeButton()}>{blockData.leftBtnName}</button>
                      </div>
                      <div className="bottom center">{ReactHtmlParser(blockData.centerBottom)}</div>
                    </div>
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

                    {value.type == 'text' &&
                      <div className="fullText margin-bottom-25"> 
                        {value.topTitle.trim() != "" && 
                          <div className="topTitle">
                            {value.topTitle}
                          </div>
                        } 
                          <div className="answerTextLeft answerLeft">
                            {ReactHtmlParser(value.titleLeft)}
                          </div>
                          <div className="answerLeft">
                            {ReactHtmlParser(value.titleRight)}
                          </div>
                      </div>
                    }

                    {value.type == 'question' &&
                      <React.Fragment>
                      <div className="fullQuestion">                      
                        {value.topTitle.trim() != "" && 
                          <div className="topTitle">
                            {value.topTitle}
                          </div>
                        }
                        <div className="question preview-question  margin-bottom-25" id={"question-" + key}>{ReactHtmlParser(value.title)}</div>
                          {value.children.length > 0 && value.children.map((answerValue, answerKey) => {
                          return(
                            <React.Fragment key={answerKey}>
                              {answerValue.topTitle.trim() != "" && 
                                <div className="topTitle">
                                  {answerValue.topTitle}
                                </div>
                              }
                              {answerValue.buttonAlign == 'bottom' &&
                                <button className='btn btn-primary answer margin-bottom-25' id={'answer-'+key+'-'+answerKey} onClick={() => this.answerQuestion(value.nodeID, answerKey, answerValue, key)}>{answerValue.title.replace(/(<([^>]+)>)/gi, "")}</button>
                              }

                              {answerValue.buttonAlign == 'right' &&
                                <React.Fragment>
                                  <div className="answerTextLeft answerLeft">
                                    {ReactHtmlParser(answerValue.rightText)}
                                  </div>
                                  {answerValue.replaceButtonWText && 
                                    <React.Fragment>
                                      {ReactHtmlParser(answerValue.secondText)}
                                    </React.Fragment>
                                  }
                                  {!answerValue.replaceButtonWText && 
                                    <button className='btn btn-primary answer answerLeft' id={'answer-'+key+'-'+answerKey} onClick={() => this.answerQuestion(value.nodeID, answerKey, answerValue, key)}>{answerValue.title.replace(/(<([^>]+)>)/gi, "")}</button>
                                  }
                                  
                                  <br />
                                </React.Fragment>
                              }
                              
                            </React.Fragment>
                            )
                          })
                        }
                        <hr />
                      </div>

                        {value.children.length && value.children.length > 0 && value.children.map((answerValue, answerKey) => {
                          return(
                            <React.Fragment key={answerKey}>
                            {answerValue.children && answerValue.children.length > 0 && answerValue.children.map((termValue, termKey) => {
                                return(
                                  <React.Fragment key={termKey}>
                                    {termValue.nodeType == 'message' && 
                                      <React.Fragment>
                                        <div className='termination hide-termination' id={'term-'+key+'-'+answerKey+'-'+termKey}>{ReactHtmlParser(termValue.title)}</div>
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
        </React.Fragment>
      );
    }
  }
  
  export default DTPreview;