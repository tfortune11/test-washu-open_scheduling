import React, { Component } from 'react';
import DataTable from 'react-data-table-component';

class startBlockTable extends Component {

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
        
        return "(None Attached)";
    }

    render() {
        const{blockList} = this.props;

        const columns = [
            {
                name: 'Start Block Name',
                selector: row => row.name,
                sortable: true,
            },
            {
                name: 'Decision Tree',
                selector: row => this.dtLookup(row.decisionTree),
                sortable: true,
            },
            {
                name: 'Shortcode',
                selector: row => '[WashUOS id="' + row.decisionTree + '"]',
                sortable: true,
            },
            {
                name: 'Edit',
                button: true,
                cell: row => <a className="makeLink pointer" onClick={() => this.props.editStartBlock({id: parseInt(row.id)})}>Edit</a>,
                sortable: true
            },
            {
                name: 'Delete',
                button: true,
                cell: row => <a className="makeLink pointer" onClick={() => {this.props.deleteStartBlock({id: parseInt(row.id)})}}>Delete</a>,
                sortable: true
            },
            { 
                name: 'Export',
                button: true,
                cell: row => <a className="makeLink pointer" href={"admin.php?page=decision-tree-builder&SB_ID=" + parseInt(row.id)}>Create File</a>,
                sortable: true
            },
        ];

        return (
           
            <div style={{marginTop:"50px", borderTop:"2px solid black"}}>
                <DataTable
                columns={columns}
                data={blockList}
                />
            </div>
        );
    }
}

export default startBlockTable;