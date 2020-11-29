import React from "react";
import Setting from './Settings'
const Admin = ({wpObject}) => {
  return (
    <div className="wrap">
      <h1>Better Wishlist 1.0.0</h1>
      <Setting wpObject={wpObject} />
    </div>
  );
};
export default Admin;
