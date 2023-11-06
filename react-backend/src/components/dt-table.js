import React, { Component } from 'react';
import DataTable from 'react-data-table-component';

class DTTable extends Component {

    constructor(props) {
        super(props);
        this.state = {
        
        }
    } 
    
    dtLookup(treeId)
    {
        if(treeId && this.props.treeList)
        {
            var dt = this.props.treeList;
            dt = dt.find(t => t.DT_ID == treeId);
            
            if(dt)
            {
                return dt.DT_ID + ' - ' + dt.DT_Name;
            }
        }
        
        return "";
    }

    sbLookup(treeId)
    {
        var sb = this.props.blockList;
        sb = sb.find(s => s.decisionTree == treeId);

        if(sb)
        {
            return sb.name;
        }

        return "(None Attached)";
    }

    render() {
        const{
            blockList, 
            treeList
        } = this.props;

        const columns = [
            {
                name: 'Decision Tree Name',
                selector: row => this.dtLookup(row.DT_ID),
                sortable: true,
            },
            {
                name: 'Start Block Name',
                selector: row => this.sbLookup(row.DT_ID),
                sortable: true,
            },
            {
                name: 'Shortcode',
                selector: row => '[WashUOS id="' + row.DT_ID + '"]',
                sortable: true,
            },
            {
                name: 'Duplicate',
                button: true,
                cell: row => <a className="makeLink pointer" onClick={() => this.props.duplicateTree({id: parseInt(row.DT_ID)})}>Duplicate</a>,
                sortable: true
            },
            {
                name: 'Edit',
                button: true,
                cell: row => <a className="makeLink pointer" onClick={() => this.props.editTree({id: parseInt(row.DT_ID)})}>Edit</a>,
                sortable: true
            },
            {
                name: 'Delete',
                button: true,
                cell: row => <a className="makeLink pointer" onClick={() => {this.props.deleteTree({id: parseInt(row.DT_ID)})}}>Delete</a>,
                sortable: true
            },
            { 
                name: 'Export',
                button: true,
                cell: row => <a className="makeLink pointer" href={"admin.php?page=decision-tree-builder&DT_ID=" + parseInt(row.DT_ID)}>Create File</a>,
                sortable: true
            },
        ];

        return (
           
            <div style={{marginTop:"50px", borderTop:"2px solid black"}}>
                <DataTable
                columns={columns}
                data={treeList}
                />
            </div>
        );
    }
}

export default DTTable;