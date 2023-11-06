import React from 'react';
import { Editor } from '@tinymce/tinymce-react';

class TerminateNode extends React.Component {
    constructor(props) {
      super(props);
  
      this.state = {
        selectedRadio:"message",      
        terminationText: "",
        

        removeEpicVal:"",
        removeProviderVal:"",

        epicDDL: [],
        visitTypeDDL: [],
        providerDDL: []
      }
    }
  
    componentDidMount() 
    {
      var node = this.props.node;
      if(node.academicVal)
      {
        this.selectAcademic(node.academicVal);
      }
    }

    removeNode = () => {
      var tIndex = ({ treeIndex }) => treeIndex;
      this.props.deleteNode(tIndex, "termination");
    }

    terminationUpdate(value)
    {
      var terminationKey = ({ treeIndex }) => treeIndex;

      var updatedNode = this.props.node;
      updatedNode.title = value;

      this.props.terminationUpdate({terminationNode: updatedNode, terminationPath: this.props.path, terminationNodeKey: terminationKey});
    }

     updateFieldValues(field, value)
     {
        var terminationKey = ({ treeIndex }) => treeIndex;

        var updatedNode = this.props.node;
       
        switch(field)
        {
          case "radio":
            updatedNode.nodeType = value;
            break;
          case 'nodeName': 
            updatedNode.nodeName = value;
            break;
          case "academicVal":
            updatedNode.academicVal = value;
            break;
          case "academicList":
            updatedNode.academicOptions = value;
            break;
          case "visitTypeVal":
            updatedNode.visitTypeVal = value;
            break;
          case "visitTypeList":
            updatedNode.visitTypeOptions = value;
            break;
          case "epicVal":
            updatedNode.epicVal = value;
            break;
          case "epicList":
            updatedNode.epicOptions = value;
            break;
          case "providerVal":
            updatedNode.providerVal = value;
            break;
          case "providerList":
            updatedNode.providerOptions = value;
            break;
          case "message":
            updatedNode.title = value;
            break;
       }

       this.props.terminationUpdate({terminationNode: updatedNode, terminationPath: this.props.path, terminationNodeKey: terminationKey});
     }

    selectAcademic(value)
    {
      var epic = this.props.epicList.filter(el => el.AD_Code == value);
      var visitType = this.props.visitTypeList.filter(vl => vl.AD_Code == value);
      var providers = this.props.providerList.filter(pl => pl.AD_Code == value);

      this.setState({
        epicDDL: epic,
        visitTypeDDL: visitType,
        providerDDL: providers
      });

      this.updateFieldValues('academicVal', value);
    }

    addSelectedValue(value)
    {
      var node = this.props.node;

      switch(value)
      {
        case "academic":
          var selectedItem = this.props.academicList.find(al => al.AD_Code == this.props.node.academicVal);
          node.academicOptions = [...node.academicOptions, selectedItem];

          this.updateFieldValues('academicList', node.academicOptions);
          break;
        case "visitType":
          var selectedItem = this.props.visitTypeList.find(vl => vl.VT_VisitTypeID == this.props.node.visitTypeVal);
          node.visitTypeOptions = [selectedItem];
          
          this.updateFieldValues('visitTypeList', node.visitTypeOptions);
          break;
        case "epic":
          var selectedItem = this.props.epicList.find(el => el.ED_Code == this.props.node.epicVal);
          node.epicOptions = [...node.epicOptions,selectedItem];
          
          this.updateFieldValues('epicList', node.epicOptions);
          break;
        case "provider":
          var selectedItem = this.props.providerList.find(pl => pl.PL_NPI == this.props.node.providerVal);
          node.providerOptions = [...node.providerOptions, selectedItem];

          this.updateFieldValues('providerList', node.providerOptions);
          break;
      }
    }


    removeSelectedValue(value)
    {
      var node = this.props.node;

      switch(value)
      {
        case "epic":
          var remainingItems = [];
          if(node.epicOptions.length > 1 && this.state.removeEpicVal != "")
          {
            remainingItems = node.epicOptions.filter(el => el.ED_Code != this.state.removeEpicVal);
          }

          this.updateFieldValues('epicList', remainingItems);
          break;
        case "provider":
          var remainingItems = [];
          if(node.providerOptions.length > 1 && this.state.removeProviderVal != "")
          {
            remainingItems = node.providerOptions.filter(pl => pl.PL_NPI != this.state.removeProviderVal);
          }

          this.updateFieldValues('providerList', remainingItems);
          break;
      }
    }
    
   render() {
      const { 
        node,
        path,
        academicList,
        editorSettings,
      } = this.props;

      const {
        removeEpicVal,
        removeProviderVal,
        visitTypeDDL,
        epicDDL,
        providerDDL
      } = this.state;

      return (
        <React.Fragment>
          <div className="container">

            <div className="row">
              <div className="col-sm-12 margin-bottom-10" style={{display:"flex"}}>
                <h2>Termination</h2>
                <div className="question-node-input">
                    <input type="text" className="form-control" placeholder="Termination name (Optional - Internal)" onChange={(e) => this.updateFieldValues('nodeName', e.target.value)} value={node.nodeName}/>
                </div>
              </div>
            </div>

            <div className="row margin-bottom-25">

              <div className="col-sm-2">
                <div className="btn-group" role="group">
                  <button type="button" className={node.nodeType == 'message' ? 'btn btn-primary' : 'btn btn-secondary'} onClick={() => this.updateFieldValues("radio", "message")}>Message</button> 
                  <button type="button" className={node.nodeType == 'epic' ? 'btn btn-primary' : 'btn btn-secondary'} onClick={() => this.updateFieldValues("radio", "epic")} >Epic</button>
                </div>
              </div>

            </div>


            {node.nodeType == 'epic' &&
              <div className="row">
                  
                  <div className="col-sm-3">

                    <div className="row">

                      <div className="col-sm-12">
                        <select className="form-select" onChange={(e) => this.selectAcademic(e.target.value)}  value={node.academicVal}> 
                          <option value="">Select Academic Department</option>
                          {academicList.length > 0 && academicList.map((value, key) => {
                            return(<option value={value.AD_Code} key={key}>{value.AD_Name}</option>)
                          })}
                        </select>
                      </div>
                    </div>
                  </div>

                  <div className="col-sm-3">

                    <div className="row">

                      <div className="col-sm-12">
                        <select className="form-select" onChange={(e) => this.updateFieldValues('visitTypeVal', e.target.value)} disabled={node.academicVal == ""} value={node.visitTypeVal}> 
                          <option value="">Select Visit Type</option>
                          {visitTypeDDL.length > 0 && visitTypeDDL.map((value, key) => {
                              return(<option value={value.VT_VisitTypeID} key={key}>{value.VT_Name}</option>)
                            })}
                        </select>
                      </div>
                    </div>
                  </div>

                  <div className="col-sm-3">

                    <div className="row">
                    
                      <div className="col-sm-12">
                        <select className="form-select" onChange={(e) => this.updateFieldValues('epicVal', e.target.value)} disabled={node.academicVal == ""} value={node.selectedEpicVal}> 
                          <option value="">Select Epic Department</option>
                          {epicDDL.length > 0 && epicDDL.map((value, key) => {
                                  return(<option value={value.ED_Code} key={key}>{value.ED_Name}</option>)
                              })}
                        </select>
                      </div>

                      <div className="col-sm-12 margin-bottom-10">
                        <div className="term-control-btns" role="group">
                          <button className="btn btn-primary margin-right-10" onClick={(e) => { e.preventDefault(); this.addSelectedValue('epic');}}>Add</button>
                          <button className="btn btn-danger" onClick={(e) => { e.preventDefault(); this.removeSelectedValue('epic');}}>Remove</button>
                        </div>
                      </div>

                      <div className="col-sm-12 margin-bottom-10">
                        <select className="form-select" size="3" onChange={(e) => this.setState({removeEpicVal: e.target.value})}  value={removeEpicVal}>
                          <option>Select Epic Dept</option>
                          {node.epicOptions && node.epicOptions.map((value, key) => {
                                return(<option value={value.ED_Code} key={key}>{value.ED_Name}</option>)
                              })}
                        </select>
                      </div>

                    </div>
                  </div>

                  <div className="col-sm-3">

                    <div className="row">

                      <div className="col-sm-12">
                        <select className="form-select" onChange={(e) => this.updateFieldValues('providerVal', e.target.value)}  disabled={node.academicVal == ""} value={node.selectedProviderVal}> 
                          <option value="">Select Provider</option>
                          {providerDDL.length > 0 && providerDDL.map((value, key) => {
                                  return(<option value={value.PL_NPI} key={key}>{value.PL_ProviderName}</option>)
                              })}
                        </select>
                      </div>

                      <div className="col-sm-12 margin-bottom-10">
                        <div className="term-control-btns" role="group">
                          <button className="btn btn-primary margin-right-10" onClick={(e) => { e.preventDefault(); this.addSelectedValue('provider');}}>Add</button>
                          <button className="btn btn-danger" onClick={(e) => { e.preventDefault(); this.removeSelectedValue('provider');}}>Remove</button>
                        </div>
                      </div>

                      <div className="col-sm-12 margin-bottom-10">
                        <select className="form-select" size="3" onChange={(e) => this.setState({removeProviderVal: e.target.value})} value={removeProviderVal}>
                          <option>Select Provider</option>
                          {node.providerOptions && node.providerOptions.map((value, key) => {
                                return(<option value={value.PL_NPI} key={key}>{value.PL_ProviderName}</option>)
                              })}
                        </select>
                      </div>
                      
                    </div>
                  </div>
              </div>
            }

            {node.nodeType == 'message' &&
              <div className="row">
                <div className="col-sm-12 margin-bottom-25">
                  <Editor
                      init={editorSettings} 
                      value={node.title} 
                      onEditorChange={(value) => this.updateFieldValues('message', value)} 
                    />
                </div>
              </div>
            }

            <div className="row">
              <div className="col-sm-12 margin-bottom-10">
                <button className="btn btn-primary" onClick={(e) => { e.preventDefault(); this.removeNode() }}>Delete Termination</button>
              </div>     
            </div>

          </div>
        </React.Fragment>
      );
    }
  }
  
  export default TerminateNode;