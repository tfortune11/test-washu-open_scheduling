import React from 'react';
/*import TinyMCE from 'react-tinymce';*/
import { Editor } from '@tinymce/tinymce-react';

class TextNode extends React.Component {
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
      this.props.newNode({type: 'text', nodePath:tPath});
    }

    removeNode = () => {
//      var tIndex = ({ treeIndex }) => treeIndex;
      this.props.deleteNode(this.props.path);
    }

    handleEditorChange = (content) => {
      this.props.addQuestion(content);
    }

    textUpdate(field, value)
    {
      var textKey = ({ treeIndex }) => treeIndex;

      var updatedNode = this.props.node;

      switch(field)
      {
        case 'topTitle': 
          updatedNode.topTitle = value;
          break;
        case 'disable': 
          updatedNode.disable = value;
          break;
        case 'nodeName': 
          updatedNode.nodeName = value;
          break;
        case 'textLeft': 
          updatedNode.titleLeft = value;
          break;
        case 'textRight': 
          updatedNode.titleRight = value;
          break;
      }
      

      this.props.textUpdate({textNode: updatedNode, textPath: this.props.path, textNodeKey: textKey});
    }
    
    render() {

      const { 
        node,
        path,
        getNodeKey,
        editorSettings,
        
      } = this.props;

      const {
        questionNum
      } = this.state;

      return (
        <React.Fragment>
          <div className="container">

            <div className="row">
                <div className="col-sm-2">
                  <h2>Text ID: {node.nodeID + 1}</h2>
                </div>
                <div className="col-sm-2">
                  <input type="text" className="form-control" placeholder="Add a name for this node" onChange={(e) => this.textUpdate('nodeName', e.target.value)} value={node.nodeName}/>
                </div>
                <div className="col-sm-2">
                  <label>Disable Text Block&nbsp;
                    <input type="checkbox" onChange={(e) => this.textUpdate('disable', e.target.checked)} checked={node.disable}/>
                  </label>
                </div>
              </div>

            <div className="row margin-bottom-25">
              <div className="col-sm-12 margin-bottom-25">
                <input type="text" className="form-control" placeholder="Add a grey title above question here (Optional)" onChange={(e) => this.textUpdate('topTitle', e.target.value)} value={node.topTitle}/>
              </div>
              <div className="col-sm-6">
                <Editor
                  init={editorSettings} 
                  value={node.titleLeft}
                  onEditorChange={(value) => this.textUpdate('textLeft', value)} 
                />
              </div>

              <div className="col-sm-6">
                <Editor
                  init={editorSettings} 
                  value={node.titleRight}
                  onEditorChange={(value) => this.textUpdate('textRight', value)} 
                />
              </div>
            </div>
          </div>
        </React.Fragment>
      );
    }
  }
  
  export default TextNode;