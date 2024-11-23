import '../css/app.css';
import React from 'react';
import { createRoot } from 'react-dom/client';
import FlightSearchForm from './components/FlightSearchForm';

function App() {
    return (
        <div className="min-h-screen bg-gray-100 p-6">
            <FlightSearchForm />
        </div>
    );
}

if (document.getElementById('app')) {
    createRoot(document.getElementById('app')).render(<App />);
}
