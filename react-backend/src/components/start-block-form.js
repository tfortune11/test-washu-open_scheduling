import React, { Component } from 'react';
import { Editor } from '@tinymce/tinymce-react';

class StartBlockForm extends Component {
    constructor(props) {
        super(props);
        this.state = {      
            saveMessage: "",
            errorMessage: "",
            imagePreviewUrl: ""
        }
    
    }
     componentDidMount() 
    {
        
    }


    getAllUsedDts(blockList, blockData)
    {
        var usedIDs = [];
        var allSb = blockList;
        var curBlock = blockData;

        // Loop through the blockList array to find a matching decisionTree
        for (const sb of allSb) {
            if (sb.hasOwnProperty('decisionTree') && sb.decisionTree !== curBlock.decisionTree) {
                usedIDs.push(sb.decisionTree);
            }
        }

        return usedIDs;
        
    }

    checkIfDtIdIsUsed(id, usedDts) {
        return usedDts.includes(id);
    }

    addStartBlock = () => {
        this.props.addStartBlockData();
        this.setState({saveMessage: "Start Block Saved"}, () => setTimeout(() => this.setState({saveMessage: ""}), 3000));
    }

    errorMessageHandling = (msg) => {
        this.setState(
            {
                errorMessage: msg
            }
        )
    }

    updateStartBlock(field, value)
    {
        var updatedSB = this.props.blockData;
        
        switch(field)
        {
            case 'name':
                updatedSB.name = value;
                break;
            case 'colFormat':
                updatedSB.colFormat = value;
                break;
            case 'title':
                updatedSB.title = value;
                break;
            case 'leftContentTop':
                updatedSB.leftContentTop = value;
                break;
            case 'leftBtnName':
                updatedSB.leftBtnName = value;
                break;
            case 'decisionTree':
                updatedSB.decisionTree = value;
                break;
            case 'picURL':
                updatedSB.picURL = value;
                break;
            case 'leftContentBottom':
                updatedSB.leftContentBottom = value;
                break;
            case 'rightContentTop':
                updatedSB.rightContentTop = value;
                break;
            case 'rightBtnName':
                updatedSB.rightBtnName = value;
                break;
            case 'rightBtnLink':
                updatedSB.rightBtnLink = value;
                break;
            case 'rightContentBottom':
                updatedSB.rightContentBottom = value;
                break;
            case 'centerBottom':
                updatedSB.centerBottom = value;
                break;
        }

        if(field == 'decisionTree')
        {
            this.props.decisionTreeUpdate(updatedSB);
        }
        else
        {
            this.props.blockUpdate(updatedSB);
        }
    }

    render() {
        const { 
            treeList,
            blockData,
            blockList,
            editorSettings,
        } = this.props;

        const {
            saveMessage,
            errorMessage,
        } = this.state;

        var addUpdateButton = (blockData.id > 0 ? "Save Start Block" : "Add New Start Block");
        blockData.colFormat = (!blockData.colFormat ? '2Column' : blockData.colFormat);

        return (
            <div className="">
                <div className="row margin-bottom-25">
                    <div className="col-sm-2">
                        <input type="text" className="form-control" placeholder="Enter Start Block Name" value={blockData.name} onChange= {(e) => this.updateStartBlock('name', e.target.value)}/>
                    </div>
                    <div className="col-sm-3">
                        <button className="btn btn-primary margin-right-25" onClick={(e) => { 
                                    e.preventDefault();
                                    if((blockData.decisionTree == 0)) {
                                        this.errorMessageHandling("Please Select Decision Tree");
                                    } else if (blockData.name == "") {
                                        this.errorMessageHandling("Please Enter Block Name");
                                    } else {
                                        this.addStartBlock(); 
                                        this.errorMessageHandling("");
                                    }
                                }}>{addUpdateButton}</button>
                                
                        {errorMessage != "" && <React.Fragment><div className="alert alert-danger">{errorMessage}</div></React.Fragment>}
                        {saveMessage != "" && <React.Fragment><div className="alert alert-success">Start Block Saved</div></React.Fragment>}
                    </div>
                    <div className="col-sm-3">
                        {blockData.id > 0 && 
                            <button className="btn btn-primary " onClick={(e) => { e.preventDefault(); this.props.previewStartBlock();}}>Preview</button>
                        }
                    </div>
                </div>
                <div className="row margin-bottom-25">
                    <div className="col-sm-12">
                        {(blockData.colFormat == 'individual') && (
                            <strong>Name Field</strong>
                        )}
                        <input type="text" className="form-control" placeholder="Enter Start Block Title" value={blockData.title} onChange= {(e) => this.updateStartBlock('title', e.target.value)}/> 
                        <br />
                        <strong>Field Format</strong>
                        <div className="row margin-bottom-25" style={{display: 'flex', flexDirection: 'row'}}>
                            <label style={{width: 'fit-content'}}>
                            <input
                                type="radio"
                                name="option"
                                value="2Column"
                                checked={blockData.colFormat === '2Column'}
                                onChange={(e) => this.updateStartBlock('colFormat', e.target.value)}
                            />  
                            2-Column
                            </label>
                            <br />
                            <label style={{width: 'fit-content'}}>
                            <input
                                type="radio"
                                name="option"
                                value="1Column"
                                checked={blockData.colFormat === '1Column'}
                                onChange={(e) => this.updateStartBlock('colFormat', e.target.value)}
                            />
                            1-Column
                            </label>
                            <br />
                            <label style={{width: 'fit-content'}}>
                            <input
                                type="radio"
                                name="option"
                                value="individual"
                                checked={blockData.colFormat === 'individual'}
                                onChange={(e) => this.updateStartBlock('colFormat', e.target.value)}
                            />
                            Individual
                            </label>
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className={`col-sm-${blockData.colFormat === '2Column' ? '6' : '12'}`}>
                        <div className="row">
                            <div className="col-sm-12 margin-bottom-25">
                            {(blockData.colFormat == 'individual') && (
                                <strong>Main Content Field</strong>
                            )}

                            {(blockData.colFormat == '2Column') && (
                                <strong>Left Field</strong>
                            )}

                            {(blockData.colFormat == '1Column') && (
                                <strong>Top Field</strong>
                            )}
                                
                            </div>
                            <div className="col-sm-12 margin-bottom-25">
                                <Editor
                                    init={editorSettings}                                    
                                    textareaName='left-content-top'
                                    value={blockData.leftContentTop}
                                    onEditorChange= {(value) => this.updateStartBlock('leftContentTop', value)}
                                /> 
                            </div>
                            <div className="col-sm-12 margin-bottom-25">
                                {(blockData.colFormat == 'individual') && (
                                    <input type="text" className="form-control" placeholder="Enter Center Decision Tree Button Text"  value={blockData.leftBtnName} onChange= {(e) => this.updateStartBlock('leftBtnName', e.target.value)} />
                                )}

                                {(blockData.colFormat == '2Column') && (
                                    <input type="text" className="form-control" placeholder="Enter Left Decision Tree Button Text"  value={blockData.leftBtnName} onChange= {(e) => this.updateStartBlock('leftBtnName', e.target.value)} />
                                )}

                                {(blockData.colFormat == '1Column') && (
                                    <input type="text" className="form-control" placeholder="Enter Center Decision Tree Button Text"  value={blockData.leftBtnName} onChange= {(e) => this.updateStartBlock('leftBtnName', e.target.value)} />
                                )}

                            </div>
                            <div className="col-sm-12 margin-bottom-25">
                                <select className="form-control form-select" value={blockData.decisionTree} onChange= {(e) => this.updateStartBlock('decisionTree', e.target.value)} > 
                                    <option value="0">Select a Tree</option>
                                    {(treeList.length > 0) && (blockList.length > 0) && treeList.map((value, key) => {
                                            
                                            console.log('logs of main form');
                                            //console.log(value);
                                            //console.log(blockList);
                                            console.log(blockData);
                                            const usedDTs = this.getAllUsedDts(blockList, blockData);
                                            console.log(value.DT_ID);
                                            console.log('this list:');
                                            console.log(blockList);
                                            console.log(usedDTs);
                                            const thisIDIsUsed = this.checkIfDtIdIsUsed(value.DT_ID, usedDTs);
                                            console.log(thisIDIsUsed);
                                            
                                            if(!thisIDIsUsed) {
                                                return(
                                                    <option key={key} value={value.DT_ID}>{value.DT_ID} - {value.DT_Name}</option>
                                                );
                                            } else {
                                                return null;
                                            }
                                            
                                            
                                    })}
                                </select>
                            </div>
                            <div className="col-sm-12 margin-bottom-25">
                                <Editor
                                    init={editorSettings} 
                                    textareaName='left-content-btm'
                                    value={blockData.leftContentBottom}
                                    onEditorChange= {(value) => this.updateStartBlock('leftContentBottom', value)}
                                />
                            </div>
                        </div>
                    </div>


                    {(blockData.colFormat === '2Column') && (
                        <div className="col-sm-6">
                            <div className="row">
                                <div className="col-sm-12 margin-bottom-25">
                                    <strong>Right Fields</strong>
                                </div>
                                <div className="col-sm-12 margin-bottom-25">
                                    <Editor
                                        init={editorSettings}
                                        textareaName='right-content-top'
                                        value={blockData.rightContentTop}
                                        onEditorChange= {(value) => this.updateStartBlock('rightContentTop', value)}
                                    />
                                </div>
                                <div className="col-sm-12 margin-bottom-25">
                                    <input type="text" placeholder="Enter Right Button Text" className="form-control" value={blockData.rightBtnName} onChange= {(e) => this.updateStartBlock('rightBtnName', e.target.value)} />
                                </div>
                                <div className="col-sm-12 margin-bottom-25">
                                    <input type="text" placeholder="Enter Right Button URL (Ex: https://physicians.wustl.edu/)" className="form-control" value={blockData.rightBtnLink} onChange= {(e) => this.updateStartBlock('rightBtnLink', e.target.value)} />
                                </div>
                                <div className="col-sm-12 margin-bottom-25">
                                    <Editor
                                        init={editorSettings}
                                        textareaName='right-content-btm'
                                        value={blockData.rightContentBottom}
                                        onEditorChange= {(value) => this.updateStartBlock('rightContentBottom', value)}
                                    />
                                </div>
                            </div>
                        </div>
                    )}

                    <div className="row">
                        <div className={`offset-sm-${blockData.colFormat == '2Column' ? '3' : '0'} col-sm-${blockData.colFormat !== '2Column' ? '12' : '6'} margin-bottom-25`}>
                            <div className="margin-bottom-25">
                                <strong>Bottom Center Field</strong>
                            </div>
                            <Editor
                                init={editorSettings}
                                textareaName='center-bottom'
                                value={blockData.centerBottom}
                                onEditorChange= {(value) => this.updateStartBlock('centerBottom', value)}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-3">
                            <button className="btn btn-primary" onClick={(e) => { 
                                    e.preventDefault();
                                    /*if((blockData.decisionTree == 0)) {
                                        this.errorMessageHandling("Please Select Decision Tree");
                                    } else*/ 
                                    if (blockData.name == "") {
                                        this.errorMessageHandling("Please Enter Block Name");
                                    }
                                    else {
                                        this.addStartBlock(); 
                                        this.errorMessageHandling("");
                                    }
                                }}>{addUpdateButton}</button>
                            {errorMessage != "" && <React.Fragment><div className="alert alert-danger">{errorMessage}</div></React.Fragment>}
                            {saveMessage != "" && <React.Fragment><div className="alert alert-success">Start Block Saved</div></React.Fragment>}
                        </div>
                        <div className="col-sm-3">
                            {blockData.id > 0 && 
                                <button className="btn btn-primary " onClick={(e) => { e.preventDefault(); this.props.previewStartBlock();}}>Preview</button>
                            }
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default StartBlockForm;