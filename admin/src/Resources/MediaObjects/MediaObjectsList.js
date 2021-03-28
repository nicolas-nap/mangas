import React from 'react';
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import { List, DeleteButton } from "react-admin";

const cardStyle = {
    width: 300,
    minHeight: 300,
    margin: '0.5em',
    display: 'inline-block',
    verticalAlign: 'top'
};
const MediaGrid = ({ ids, data, basePath }) => (
    <div style={{ margin: '1em' }}>
    {ids.map(id =>
        <Card key={id} style={cardStyle}>
            <CardContent>
                <img src={data[id].contentUrl} alt="" />
            </CardContent>
            <CardActions style={{ textAlign: 'right' }}>
                <DeleteButton record={data[id]} />
            </CardActions>
        </Card>
    )}
    </div>
);
MediaGrid.defaultProps = {
    data: {},
    ids: [],
};


const MediaObjectsList = (props) => {
    return (
        <List {...props}>
            <MediaGrid />
        </List>
    );
};
export default MediaObjectsList;