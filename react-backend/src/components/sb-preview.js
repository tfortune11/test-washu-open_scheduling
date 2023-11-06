import React, { Component } from 'react';
import parse from 'html-react-parser';

export default class SBPreview extends Component {

    render() {
        const { 
            sbData
        } = this.props;
        return (
            <div>
                <div className="sb-preview">
                    <h2>{sbData.title}</h2>
                    <div className="sb-preview-cols">
                        <div className="sb-preview-col">
                            <span>{parse(sbData.leftContentTop)}</span>
                            <a className="preview-btn" href={sbData.leftBtnLink}>{sbData.leftBtnName}</a>
                            <span>{parse(sbData.leftContentBottom)}</span>
                        </div>
                        <div className="sb-preview-col">
                            <span>{parse(sbData.rightContentTop)}</span>
                            <a className="preview-btn" href={sbData.rightBtnLink}>{sbData.rightBtnName}</a>
                            <span>{parse(sbData.rightContentBottom)}</span>
                        </div>
                    </div>
                    <div>{parse(sbData.centerBottom)}</div>
                </div>              
            </div>
        )
    }
}
