import React from 'react';
import { Editor } from '@tinymce/tinymce-react';
import parse from 'html-react-parser';
class AnswerNode extends React.Component {
    constructor(props) {
      super(props);
  
      this.state = {      

      }
    }
  
    componentDidMount() 
    {
      
    }

    insertNewNode = () => {

      if(this.props.node.children.length < 1)
      {
        var tPath = this.props.path;
        this.props.newNode({type: 'termination', nodePath: tPath});
      }
    }

    removeNode = () => {
      var tIndex = ({ treeIndex }) => treeIndex;
      this.props.deleteNode(tIndex, "answer");
    }

    updateFieldValues(field, value)
    {
      var answerKey = ({ treeIndex }) => treeIndex;

      var updatedNode = this.props.node;
      
      switch(field)
      {
        case "topTitle":
          updatedNode.topTitle = value;
          break;
        case 'nodeName': 
          updatedNode.nodeName = value;
        break;
        case "radio":
          updatedNode.goToType = value;
          break;
        case "question":
          updatedNode.goToGuid = value;
          break;
        case "tree":
          updatedNode.goToGuid = value;
          break;
        case "rightText":
          updatedNode.rightText = value;
          break;
        case "buttonAlign":
          updatedNode.buttonAlign = value;
          break;
        case "message":
          updatedNode.title = value;
          break;
        case "replaceButtonWText":
          updatedNode.replaceButtonWText = value;
          break;
        case "secondText":
          updatedNode.secondText = value;
          break;
      }

      this.props.answerUpdate({answerNode: updatedNode, answerPath: this.props.path, answerNodeKey: answerKey});
    }

    updateDropdown(radioSelected)
    {
      this.updateFieldValues('radio', radioSelected);      
    }

    answerUpdate(value)
    {
      var answerKey = ({ treeIndex }) => treeIndex;

      var updatedNode = this.props.node;
      updatedNode.title = value;

      this.props.answerUpdate({answerNode: updatedNode, answerPath: this.props.path, answerNodeKey: answerKey});
    }

    onValueChange(event) {
      this.setState({
        selectedOption: event.target.value
      });
    }

    render() {

      const { 
        node,
        questionList,
        treeList,
        treeData,
        editorSettings,
      } = this.props;

      const {
        ddlList
      } = this.state;

      return (
        <React.Fragment>
        <div className="container" style={{minWidth: '960px'}}>

          <div className="row">
            <div className="col-sm-2">
              <h2>Answer</h2>
            </div>
            <div className="col-sm-4">
              <input type="text" className="form-control" placeholder="Answer name (Optional - Internal)" onChange={(e) => this.updateFieldValues('nodeName', e.target.value)} value={node.nodeName}/>
            </div>
          </div>

          <div className="row">
            <div className="col-sm-12 margin-bottom-25">
              <input type="text" className="form-control" placeholder="Add a grey title above answer (Optional - Shown to user)" onChange={(e) => this.updateFieldValues('topTitle', e.target.value)} value={node.topTitle}/>
            </div>
          </div>
          <div className="row margin-bottom-25">
            <div className="col-sm-12">



              <div className="row margin-bottom-25">
              {node.buttonAlign == 'right' && 
                <React.Fragment>
                  <div className="col-sm-6"> 
                    <Editor
                        init={editorSettings} 
                        value={node.rightText}
                        onEditorChange={(value) => this.updateFieldValues('rightText', value)} 
                      />
                    </div>
                    <div className="col-sm-1">&nbsp;</div>
                  </React.Fragment>
                }
                
                <div className="col-sm-4">
                  <div className="row">
                    <div className="col-sm-12 margin-bottom-25">
                      {!node.replaceButtonWText && 
                        <input type="text" className="form-control" placeholder="Enter answer text" onChange={(e) => this.answerUpdate(e.target.value)} value={node.title}/>
                      }
                      {node.buttonAlign == "right" && 
                        <label><input type="checkbox" onChange={(e) => this.updateFieldValues('replaceButtonWText', e.target.checked)} checked={node.replaceButtonWText} /> Replace Button With Text</label>
                      }
                      {node.replaceButtonWText && 
                        <Editor
                          init={{
                            height: 200,
                            menubar: false,
                            placeholder: 'Enter text here',
                            force_br_newlines : true,
                            force_p_newlines : false,
                            forced_root_block : '',
                            cleanup : false,
                            verify_html : false,
                            relative_urls : false,
                            remove_script_host : false,
                            extended_valid_elements: 'i[class]',
                            plugins: [
                              'advlist autolink lists link image charmap print preview anchor',
                              'searchreplace visualblocks code fullscreen',
                              'insertdatetime media table paste code wordcount'
                            ],
                            toolbar: 'undo redo | formatselect | ' +
                            'bold italic backcolor | alignleft aligncenter ' +
                            'alignright alignjustify | bullist numlist outdent indent | ' +
                            'removeformat | link image | code',
                            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
                          }}
                          value={node.secondText}
                          onEditorChange={(value) => this.updateFieldValues('secondText', value)} 
                        />
                      }
                    </div>
                  </div>
                  <div className="row">
                    <div className="col-sm-12">
                      <select className="form-select form-control" onChange={(e) => this.updateFieldValues('buttonAlign', e.target.value)} value={node.buttonAlign}>
                        <option value="bottom">Button Below Question</option>
                        <option value="right">Button Right of Question</option>
                      </select>
                    </div>
                  </div>
                  {!node.replaceButtonWText && 
                    <React.Fragment>
                      <div className="row">
                        <div className="col-sm-12">
                          <h3>Go To</h3>
                        </div>
                      </div>
                      <div className="row margin-bottom-25">
                          <div className="row">
                            <div className="col-sm-12">
                              <div className="btn-group" role="group">
                                <button type="button" className={node.goToType == 'question' ? 'btn btn-primary' : 'btn btn-secondary'} onClick={() => this.updateDropdown('question')}>Question</button>
                                {/*<button type="button" className={node.goToType == 'tree' ? 'btn btn-primary' : 'btn btn-secondary'} onClick={() => this.updateDropdown('tree')}>Decision Tree</button>*/}
                                <button type="button" className={node.goToType == 'termination' ? 'btn btn-primary' : 'btn btn-secondary'} onClick={() => this.updateDropdown('termination')}>Termination</button>
                              </div>
                            </div>
                          </div>
                      </div>


                      <div className="row">

                          {node.goToType == "question" &&
                            <div className="row">
                              <div className="col-sm-12">
                                <select className="form-select" onChange={(e) => this.updateFieldValues('question', e.target.value)} value={node.goToGuid}> 
                                  {treeData.length > 0 && treeData.map((value, key) => {return(
                                    <option key={key} value={value.guid}>{key + 1} - {value.nodeName.substring(0, 45).replace(/(<([^>]+)>)/gi, "")}</option>
                                    )}
                                  )}
                                
                                </select>
                              </div>
                            </div>
                          }
                          {node.goToType == "tree" &&
                            <div className="col-sm-12">
                              <select className="form-select-tree form-control" onChange={(e) => this.updateFieldValues('question', e.target.value)} value={node.goToVal}> 
                                {ddlList.length > 0 && ddlList.map((value, key) => {return(
                                  <option key={key} value={key}>{key} - {value}</option>
                                  )}
                                )}
                              </select>
                            </div>
                          }
                      </div>
                  </React.Fragment>
                }


                </div>

              </div>
              
            </div>  

          </div>
          <div className="row">
            <div className="col-sm-6">
              {node.goToType == "termination" && node.children.length < 1 && 
                <button className="btn btn-primary margin-right-25" onClick={(e) => { e.preventDefault(); this.insertNewNode(); }}>Add Termination</button>
              } 
              <button className="btn btn-secondary" onClick={(e) => { e.preventDefault(); this.removeNode() }}>Delete Answer</button>
            </div>
          </div>
        </div>
          
        </React.Fragment>
      );
    }
  }
  
  export default AnswerNode;