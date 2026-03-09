import React from 'react';
import { ScrollView, Text, View, TextInput, Button } from 'react-native';

export default function App() {
  return (
    <ScrollView style={{ padding: 20, marginTop: 40 }}>
      <Text style={{ fontSize: 24, fontWeight: '700' }}>Cyber Sathi Mobile</Text>
      <Text>User registration, complaint help, quiz, certificate and chat APIs can be connected with the web backend.</Text>
      <View style={{ marginTop: 20 }}>
        <Text>Register</Text>
        <TextInput placeholder="Name" style={{ borderWidth: 1, marginBottom: 8, padding: 8 }} />
        <TextInput placeholder="Email" style={{ borderWidth: 1, marginBottom: 8, padding: 8 }} />
        <Button title="Submit" onPress={() => {}} />
      </View>
    </ScrollView>
  );
}
