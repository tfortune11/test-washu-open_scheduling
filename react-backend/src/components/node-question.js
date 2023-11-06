import React from 'react';
/*import TinyMCE from 'react-tinymce';*/
import { Editor } from '@tinymce/tinymce-react';

class QuestionNode extends React.Component {
    constructor(props) {
      super(props);
  
      this.state = {   
        questionNum:0   
      }      
    }

    componentDidMount() 
    {
    
    }

    insertNewNode = () => {
      var tIndex = ({ treeIndex }) => treeIndex;
      var tPath = this.props.path;
      this.props.newNode({type: 'answer', nodePath:tPath});
    }

    removeNode = () => {
//      var tIndex = ({ treeIndex }) => treeIndex;
      this.props.deleteNode(this.props.path);
    }

    handleEditorChange = (content) => {
      this.props.addQuestion(content);
    }

    questionUpdate(field, value)
    {
      var questionKey = ({ treeIndex }) => treeIndex;

      var updatedNode = this.props.node;

      switch(field)
      {
        case 'topTitle': 
          updatedNode.topTitle = value;
          break;
        case 'clearAfterAnswer': 
          updatedNode.clearAfterAnswer = value;
          break;
        case 'nodeName': 
          updatedNode.nodeName = value;
          break;
        case 'question': 
          updatedNode.title = value;
          break;
      }
      

      this.props.questionUpdate({questionNode: updatedNode, questionPath: this.props.path, questionNodeKey: questionKey});
    }
    
    render() {

      const { 
        node,
        path,
        getNodeKey,
        editorSettings
      } = this.props;

      const {
        questionNum
      } = this.state;

      return (
        <React.Fragment>
          <div className="container question-node">

            <div className="row">
                <div className="col-sm-3">
                  <h2>Question ID: {node.nodeID + 1}</h2>
                </div>
                <div className="col-sm-3">
                  <input type="text" className="form-control" placeholder="Quesiton Name (Optional - Internal)" onChange={(e) => this.questionUpdate('nodeName', e.target.value)} value={node.nodeName}/>
                </div>
                <div className="col-sm-2">
                  <label>Clear After Answer&nbsp;
                    <input type="checkbox" onChange={(e) => this.questionUpdate('clearAfterAnswer', e.target.checked)} checked={node.clearAfterAnswer}/>
                  </label>
                </div>
            </div>

            <div className="row margin-bottom-25">
              <div className="col-sm-12 margin-bottom-25">
                <input type="text" className="form-control" placeholder="Add a grey title above question (Optional - Shown to user)" onChange={(e) => this.questionUpdate('topTitle', e.target.value)} value={node.topTitle}/>
              </div>
              <div className="col-sm-12">
                <Editor
                  init={editorSettings} 
                  value={node.title}
                  onEditorChange={(value) => this.questionUpdate('question', value)} 
                />
              </div>
            </div>
            <div className="row">
              <div className="col-sm-12">
                <button className="btn btn-primary margin-right-25" onClick={(e) => { e.preventDefault(); this.insertNewNode(); }}>Add Answer</button>
                <button className="btn btn-secondary" onClick={(e) => { e.preventDefault(); this.removeNode() }}>Delete Question</button>
              </div>
            </div>
          </div>
        </React.Fragment>
      );
    }
  }
  
  export default QuestionNode;